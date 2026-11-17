<?php
namespace AJMotech\Cache\Drivers;

use AJMotech\Cache\CacheDriverInterface;

if (!defined('ABSPATH')) exit;

final class FileCacheDriver implements CacheDriverInterface
{
    private string $path;

    public function __construct(?string $path = null)
    {
        $this->path = rtrim($path ?: WP_CONTENT_DIR . '/cache/ajm-data-cache-v2/data', '/\\');
        $this->protectRoot();
    }

    /* Lê somente o arquivo correspondente à chave, removendo-o quando seu TTL expirou. */
    public function get(string $key, bool &$found = null)
    {
        $found = false;
        $file = $this->file($key);
        if (!is_file($file)) return null;
        $raw = @file_get_contents($file);
        if ($raw === false || $raw === '') return null;
        if (strncmp($raw, 'GZ1', 3) === 0) {
            $decoded = function_exists('gzdecode') ? @gzdecode(substr($raw, 3)) : false;
            if ($decoded === false) return null;
            $raw = $decoded;
        }
        $payload = @unserialize($raw, array('allowed_classes' => true));
        if (!is_array($payload) || !array_key_exists('value', $payload)) return null;
        $expires = (int)($payload['expires'] ?? 0);
        if ($expires > 0 && $expires <= time()) {
            @unlink($file);
            return null;
        }
        $found = true;
        return $payload['value'];
    }

    /* Grava o valor serializado com TTL usando arquivo temporário e rename atômico. */
    public function set(string $key, $value, int $ttl): bool
    {
        $file = $this->file($key);
        if (!$this->ensureDirectory(dirname($file))) return false;
        $raw = serialize(array('expires' => $ttl > 0 ? time() + $ttl : 0, 'value' => $value));
        if (function_exists('gzencode') && function_exists('gzdecode') && strlen($raw) >= 1024) {
            $compressed = @gzencode($raw, 5);
            if (is_string($compressed)) $raw = 'GZ1' . $compressed;
        }
        $tmp = $file . '.' . getmypid() . '.' . wp_rand(1000, 9999) . '.tmp';
        if (@file_put_contents($tmp, $raw, LOCK_EX) === false) return false;
        if (@rename($tmp, $file)) return true;
        @unlink($tmp);
        return false;
    }

    /* Exclui somente o arquivo correspondente à chave informada. */
    public function delete(string $key): bool
    {
        $file = $this->file($key);
        return !is_file($file) || @unlink($file);
    }

    /* Retorna o identificador do driver para diagnósticos. */
    public function name(): string
    {
        return 'file';
    }

    /* Expõe o diretório físico para o coletor de lixo agendado. */
    public function path(): string
    {
        return $this->path;
    }

    /* Resolve a chave para um caminho distribuído em 256 subdiretórios. */
    private function file(string $key): string
    {
        $hash = hash('sha256', $key);
        return $this->path . '/' . substr($hash, 0, 2) . '/' . $hash . '.cache';
    }

    /* Garante diretórios graváveis sem escanear arquivos existentes. */
    private function ensureDirectory(string $directory): bool
    {
        if (is_dir($directory)) return is_writable($directory);
        if (!function_exists('wp_mkdir_p')) require_once ABSPATH . 'wp-admin/includes/file.php';
        return wp_mkdir_p($directory) && is_writable($directory);
    }

    /* Adiciona arquivos de proteção no diretório raiz do cache quando possível. */
    private function protectRoot(): void
    {
        if (!$this->ensureDirectory($this->path)) return;
        $root = dirname($this->path);
        if (!is_file($root . '/index.php')) @file_put_contents($root . '/index.php', "<?php\n// Silence is golden.\n", LOCK_EX);
        if (!is_file($root . '/.htaccess')) @file_put_contents($root . '/.htaccess', "Deny from all\n", LOCK_EX);
        if (!is_file($root . '/web.config')) @file_put_contents($root . '/web.config', '<configuration><system.webServer><authorization><deny users="*" /></authorization></system.webServer></configuration>', LOCK_EX);
    }
}
