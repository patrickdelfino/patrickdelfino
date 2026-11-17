<?php

declare(strict_types=1);

// Harness de CLI para o recorte do portfólio. As classes do projeto permanecem intactas.
if (PHP_SAPI !== 'cli') {
    exit(1);
}

define('ABSPATH', __DIR__ . '/');
define('AJM_OPTIONS', 'portfolio_fixture');
define('WP_CONTENT_DIR', sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'donghua-cache-' . bin2hex(random_bytes(8)));

function get_option(string $key, $default = false)
{
    return $key === AJM_OPTIONS ? ['cache_v2_metrics_enabled' => false, 'cache_v2_ttl_jitter' => 0] : $default;
}

function sanitize_key(string $value): string
{
    return preg_replace('/[^a-z0-9_\-]/', '', strtolower($value));
}

function wp_rand(int $min, int $max): int
{
    return random_int($min, $max);
}

function absint($value): int
{
    return abs((int) $value);
}

function wp_mkdir_p(string $path): bool
{
    return is_dir($path) || mkdir($path, 0700, true);
}

function confirmar(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

spl_autoload_register(static function (string $class): void {
    $prefix = 'AJMotech\\Cache\\';
    if (str_starts_with($class, $prefix)) {
        require __DIR__ . '/src/Cache/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    }
});

use AJMotech\Cache\CacheLock;
use AJMotech\Cache\CacheManager;

try {
    $cache = new CacheManager();
    $builds = 0;
    $load = static function () use (&$builds): string {
        return 'catalogo-' . ++$builds;
    };

    confirmar($cache->remember('home', $load, 120, 'catalogo', ['obra:17']) === 'catalogo-1', 'Falha ao criar a entrada.');
    $fresh = new CacheManager();
    confirmar($fresh->remember('home', $load, 120, 'catalogo', ['obra:17']) === 'catalogo-1', 'A entrada deveria persistir entre instâncias.');
    confirmar($cache->set('sidebar', false, 120, 'catalogo', ['obra:20']), 'Falha ao gravar um valor falso.');
    $fresh->invalidateTags(['obra:17']);
    confirmar($fresh->remember('home', $load, 120, 'catalogo', ['obra:17']) === 'catalogo-2', 'A obra alterada deveria ser reconstruída.');
    confirmar($fresh->get('sidebar', 'catalogo', ['obra:20'], 'ausente') === false, 'A tag de outra obra deveria preservar a entrada.');
    $fresh->invalidateGroup('catalogo');
    confirmar($fresh->get('sidebar', 'catalogo', ['obra:20'], 'ausente') === 'ausente', 'A troca de geração deveria invalidar o grupo.');

    $first = new CacheLock();
    $second = new CacheLock();
    confirmar($first->acquire('verificacao', 'file'), 'O primeiro leitor deveria adquirir o lock.');
    confirmar(!$second->acquire('verificacao', 'file'), 'O segundo leitor deveria aguardar o lock.');
    $first->release('verificacao', 'file');
    confirmar($second->acquire('verificacao', 'file'), 'O lock deveria estar disponível após a liberação.');
    $second->release('verificacao', 'file');
    echo "Cache em arquivo, invalidação por tag e grupo e exclusão por lock verificados.\n";
} finally {
    $root = realpath(WP_CONTENT_DIR);
    $temp = realpath(sys_get_temp_dir());
    if ($root && $temp && dirname($root) === $temp && str_starts_with(basename($root), 'donghua-cache-')) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            $path = $file->getPathname();
            if ($file->isLink() || !$file->isDir()) {
                unlink($path);
            } else {
                rmdir($path);
            }
        }
        rmdir($root);
    }
}
