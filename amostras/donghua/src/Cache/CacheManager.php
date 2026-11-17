<?php
namespace AJMotech\Cache;

use AJMotech\Cache\Drivers\FileCacheDriver;
use AJMotech\Cache\Drivers\ObjectCacheDriver;

if (!defined('ABSPATH')) exit;

final class CacheManager
{
    private CacheDriverInterface $driver;
    private CacheVersionStore $versions;
    private CacheLock $lock;
    private array $memory = array();

    public function __construct(?CacheDriverInterface $driver = null, ?CacheVersionStore $versions = null, ?CacheLock $lock = null)
    {
        $this->versions = $versions ?: new CacheVersionStore();
        $this->driver = $driver ?: $this->resolveDriver();
        $this->lock = $lock ?: new CacheLock();
    }

    /* Retorna uma chave do cache respeitando geração global, grupo e tags. */
    public function get(string $key, string $group = 'default', array $tags = array(), $default = null)
    {
        if (!CacheSettings::enabled()) return $default;
        $storageKey = $this->storageKey($key, $group, $tags);
        if (array_key_exists($storageKey, $this->memory)) {
            CacheMetrics::hit('memory');
            return $this->memory[$storageKey];
        }
        $found = false;
        $value = $this->driver->get($storageKey, $found);
        if (!$found) {
            CacheMetrics::miss();
            return $default;
        }
        CacheMetrics::hit('driver');
        return $this->memory[$storageKey] = $value;
    }

    /* Salva um valor com TTL, jitter e a mesma assinatura de geração usada na leitura. */
    public function set(string $key, $value, int $ttl = 0, string $group = 'default', array $tags = array()): bool
    {
        if (!CacheSettings::enabled()) return true;
        $ttl = $ttl > 0 ? $ttl : CacheSettings::defaultTtl();
        $ttl = $this->jitterTtl($ttl);
        $storageKey = $this->storageKey($key, $group, $tags);
        $this->memory[$storageKey] = $value;
        $saved = $this->driver->set($storageKey, $value, $ttl);
        if ($saved) CacheMetrics::set();
        return $saved;
    }

    /* Executa o callback somente em cache miss e usa lock curto para reduzir stampede em reconstruções concorrentes. */
    public function remember(string $key, callable $callback, int $ttl = 0, string $group = 'default', array $tags = array())
    {
        if (!CacheSettings::enabled()) return $callback();
        $storageKey = $this->storageKey($key, $group, $tags);
        if (array_key_exists($storageKey, $this->memory)) {
            CacheMetrics::hit('memory');
            return $this->memory[$storageKey];
        }
        $found = false;
        $value = $this->driver->get($storageKey, $found);
        if ($found) {
            CacheMetrics::hit('driver');
            return $this->memory[$storageKey] = $value;
        }
        $driverName = $this->driver->name();
        $acquired = $this->lock->acquire($storageKey, $driverName);
        if (!$acquired && CacheSettings::lockWaitMs() > 0) {
            $deadline = microtime(true) + (CacheSettings::lockWaitMs() / 1000);
            do {
                usleep(20000);
                $found = false;
                $value = $this->driver->get($storageKey, $found);
                if ($found) {
                    CacheMetrics::lockWait(false);
                    CacheMetrics::hit('wait');
                    return $this->memory[$storageKey] = $value;
                }
            } while (microtime(true) < $deadline);
            CacheMetrics::lockWait(true);
        }

        CacheMetrics::miss();
        $started = microtime(true);
        try {
            $value = $callback();
            CacheMetrics::build($group, $key, (microtime(true) - $started) * 1000);
            $this->set($key, $value, $ttl, $group, $tags);
            return $value;
        } finally {
            if ($acquired) $this->lock->release($storageKey, $driverName);
        }
    }

    /* Remove a chave física exata da geração atual; invalidação lógica deve preferir tags. */
    public function delete(string $key, string $group = 'default', array $tags = array()): bool
    {
        $storageKey = $this->storageKey($key, $group, $tags);
        unset($this->memory[$storageKey]);
        return $this->driver->delete($storageKey);
    }

    /* Invalida uma ou várias tags em O(n tags), sem enumerar itens cacheados. */
    public function invalidateTags(array $tags): void
    {
        $tags = array_values(array_unique(array_filter(array_map('strval', $tags))));
        if (!$tags) return;
        $this->versions->bumpTags($tags);
        $this->memory = array();
        CacheMetrics::invalidation(count($tags));
        $this->log('Tags invalidadas: ' . implode(', ', $tags));
    }

    /* Invalida somente um grupo lógico por troca de geração. */
    public function invalidateGroup(string $group): void
    {
        $this->versions->bumpGroup($group);
        $this->memory = array();
        CacheMetrics::invalidation();
        $this->log('Grupo invalidado: ' . $group);
    }

    /* Invalida todo o cache lógico em operação constante; arquivos antigos são limpos depois pelo cron. */
    public function flush(): void
    {
        $this->versions->bumpGlobal();
        $this->memory = array();
        CacheMetrics::invalidation();
        $this->log('Geração global invalidada.');
    }

    /* Retorna dados do driver e ambiente para a tela/barra administrativa. */
    public function status(): array
    {
        return array(
            'enabled' => CacheSettings::enabled(),
            'driver' => $this->driver->name(),
            'persistent_object_cache' => function_exists('wp_using_ext_object_cache') && wp_using_ext_object_cache(),
            'ttl' => CacheSettings::defaultTtl(),
            'ttl_jitter' => CacheSettings::ttlJitterPercent(),
            'lock_wait_ms' => CacheSettings::lockWaitMs(),
            'metrics' => CacheSettings::metricsEnabled(),
        );
    }

    /* Retorna o driver ativo para operações internas de manutenção. */
    public function driver(): CacheDriverInterface
    {
        return $this->driver;
    }

    /* Monta a chave física estável sem expor a chave lógica original no filesystem ou Redis. */
    private function storageKey(string $key, string $group, array $tags): string
    {
        $group = sanitize_key($group) ?: 'default';
        $signature = $this->versions->signature($group, $tags);
        return hash('sha256', 'ajm-cache-v2|' . $group . '|' . $signature . '|' . $key);
    }

    /* Aplica pequena variação ao TTL para que conjuntos de chaves não expirem todos no mesmo instante. */
    private function jitterTtl(int $ttl): int
    {
        $ttl = max(1, $ttl);
        $percent = CacheSettings::ttlJitterPercent();
        if ($ttl < 60 || $percent <= 0) return $ttl;
        $delta = max(1, (int)round($ttl * ($percent / 100)));
        return max(30, $ttl + wp_rand(-$delta, $delta));
    }

    /* Escolhe object cache persistente quando disponível e usa arquivos como fallback realmente persistente. */
    private function resolveDriver(): CacheDriverInterface
    {
        $mode = CacheSettings::driver();
        $persistent = function_exists('wp_using_ext_object_cache') && wp_using_ext_object_cache();
        if (($mode === 'object' || $mode === 'auto') && $persistent) return new ObjectCacheDriver();
        return new FileCacheDriver();
    }

    /* Grava diagnóstico somente quando explicitamente habilitado no Codestar. */
    private function log(string $message): void
    {
        if (CacheSettings::debug() && defined('WP_DEBUG') && WP_DEBUG) error_log('[AJM Cache v2] ' . $message);
    }
}
