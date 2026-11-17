<?php
namespace AJMotech\Cache;

if (!defined('ABSPATH')) exit;

final class CacheVersionStore
{
    private string $path;
    private array $memory = array();

    public function __construct(?string $path = null)
    {
        $this->path = rtrim($path ?: WP_CONTENT_DIR . '/cache/ajm-data-cache-v2/.versions', '/\\');
    }

    /* Retorna a geração persistente de um escopo usando arquivo exato, sem varrer diretórios. */
    public function get(string $scope): string
    {
        $scope = $this->normalize($scope);
        if (isset($this->memory[$scope])) return $this->memory[$scope];
        $file = $this->file($scope);
        if (is_file($file)) {
            $value = trim((string)@file_get_contents($file));
            return $this->memory[$scope] = ($value !== '' ? $value : '1');
        }
        if ($this->ensureDirectory(dirname($file))) return $this->memory[$scope] = '1';
        $value = (string)get_option('ajm_cache_v2_ver_' . md5($scope), '1');
        return $this->memory[$scope] = ($value !== '' ? $value : '1');
    }

    /* Troca a geração de um escopo por um token monotônico e torna chaves antigas inalcançáveis instantaneamente. */
    public function bump(string $scope): string
    {
        $scope = $this->normalize($scope);
        $token = str_replace('.', '', sprintf('%.6F', microtime(true))) . wp_rand(100, 999);
        $file = $this->file($scope);
        if ($this->ensureDirectory(dirname($file)) && $this->atomicWrite($file, $token)) {
            $this->memory[$scope] = $token;
            return $token;
        }
        update_option('ajm_cache_v2_ver_' . md5($scope), $token, false);
        return $this->memory[$scope] = $token;
    }

    /* Retorna uma assinatura compacta combinando geração global, grupo e tags informadas. */
    public function signature(string $group, array $tags = array()): string
    {
        $scopes = array('global', 'group:' . sanitize_key($group));
        foreach ($this->normalizeTags($tags) as $tag) $scopes[] = 'tag:' . $tag;
        $parts = array();
        foreach ($scopes as $scope) $parts[] = $scope . '=' . $this->get($scope);
        return hash('sha256', implode('|', $parts));
    }

    /* Incrementa a geração global sem apagar arquivos fisicamente durante a requisição. */
    public function bumpGlobal(): void
    {
        $this->bump('global');
    }

    /* Incrementa somente a geração de um grupo lógico. */
    public function bumpGroup(string $group): void
    {
        $this->bump('group:' . sanitize_key($group));
    }

    /* Incrementa uma ou várias tags, deduplicando-as antes de tocar o armazenamento. */
    public function bumpTags(array $tags): void
    {
        foreach ($this->normalizeTags($tags) as $tag) $this->bump('tag:' . $tag);
    }

    /* Normaliza tags para uma representação estável e segura. */
    private function normalizeTags(array $tags): array
    {
        $output = array();
        foreach ($tags as $tag) {
            $tag = strtolower(trim((string)$tag));
            if ($tag === '') continue;
            $tag = preg_replace('/[^a-z0-9:_\-.]/', '-', $tag);
            if ($tag !== '') $output[$tag] = $tag;
        }
        ksort($output);
        return array_values($output);
    }

    /* Normaliza o nome interno de um escopo antes de gerar seu hash de arquivo. */
    private function normalize(string $scope): string
    {
        $scope = strtolower(trim($scope));
        return $scope !== '' ? $scope : 'global';
    }

    /* Resolve o arquivo exato de versão em uma árvore rasa baseada em hash. */
    private function file(string $scope): string
    {
        $hash = hash('sha256', $scope);
        return $this->path . '/' . substr($hash, 0, 2) . '/' . $hash . '.ver';
    }

    /* Garante o diretório necessário sem executar qualquer varredura recursiva. */
    private function ensureDirectory(string $directory): bool
    {
        if (is_dir($directory)) return is_writable($directory);
        if (!function_exists('wp_mkdir_p')) require_once ABSPATH . 'wp-admin/includes/file.php';
        return wp_mkdir_p($directory) && is_writable($directory);
    }

    /* Escreve um pequeno arquivo de versão de modo atômico usando rename no mesmo diretório. */
    private function atomicWrite(string $file, string $contents): bool
    {
        $tmp = $file . '.' . getmypid() . '.' . wp_rand(1000, 9999) . '.tmp';
        if (@file_put_contents($tmp, $contents, LOCK_EX) === false) return false;
        if (@rename($tmp, $file)) return true;
        @unlink($tmp);
        return false;
    }
}
