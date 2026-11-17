<?php
namespace AJMotech\Cache;

if (!defined('ABSPATH')) exit;

final class CacheLock
{
    private array $handles = array();
    private string $path;

    public function __construct(?string $path = null)
    {
        $this->path = rtrim($path ?: WP_CONTENT_DIR . '/cache/ajm-data-cache-v2/locks', '/\\');
    }

    /* Adquire um lock curto por chave para impedir várias reconstruções simultâneas do mesmo cache. */
    public function acquire(string $key, string $driver, int $ttl = 15): bool
    {
        $hash = hash('sha256', $key);
        if ($driver === 'object' && function_exists('wp_using_ext_object_cache') && wp_using_ext_object_cache()) {
            return (bool)wp_cache_add($hash, time(), 'ajm_cache_v2_locks', max(5, $ttl));
        }
        $file = $this->file($hash);
        if (!$this->ensureDirectory(dirname($file))) return false;
        $handle = @fopen($file, 'c+');
        if (!$handle) return false;
        if (!@flock($handle, LOCK_EX | LOCK_NB)) {
            @fclose($handle);
            return false;
        }
        @ftruncate($handle, 0);
        @fwrite($handle, (string)time());
        @fflush($handle);
        $this->handles[$hash] = $handle;
        return true;
    }

    /* Libera o lock adquirido pelo processo atual sem depender de exclusão recursiva de arquivos. */
    public function release(string $key, string $driver): void
    {
        $hash = hash('sha256', $key);
        if ($driver === 'object' && function_exists('wp_using_ext_object_cache') && wp_using_ext_object_cache()) {
            wp_cache_delete($hash, 'ajm_cache_v2_locks');
            return;
        }
        if (!isset($this->handles[$hash])) return;
        $handle = $this->handles[$hash];
        @flock($handle, LOCK_UN);
        @fclose($handle);
        @unlink($this->file($hash));
        unset($this->handles[$hash]);
    }

    /* Resolve o arquivo exato do lock em subdiretórios distribuídos pelo hash. */
    private function file(string $hash): string
    {
        return $this->path . '/' . substr($hash, 0, 2) . '/' . $hash . '.lock';
    }

    /* Garante o diretório necessário somente quando um lock de arquivo realmente for usado. */
    private function ensureDirectory(string $directory): bool
    {
        if (!is_dir($directory)) {
            if (!function_exists('wp_mkdir_p')) require_once ABSPATH . 'wp-admin/includes/file.php';
            if (!wp_mkdir_p($directory)) return false;
        }
        if (!is_writable($directory)) return false;
        $root = dirname(dirname($directory));
        if (!is_file($root . '/index.php')) @file_put_contents($root . '/index.php', "<?php\n// Silence is golden.\n", LOCK_EX);
        if (!is_file($root . '/.htaccess')) @file_put_contents($root . '/.htaccess', "Deny from all\n", LOCK_EX);
        return true;
    }
}
