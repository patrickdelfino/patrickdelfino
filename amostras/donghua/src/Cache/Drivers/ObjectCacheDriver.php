<?php
namespace AJMotech\Cache\Drivers;

use AJMotech\Cache\CacheDriverInterface;

if (!defined('ABSPATH')) exit;

final class ObjectCacheDriver implements CacheDriverInterface
{
    private string $group = 'ajm_cache_v2';

    /* Lê uma chave usando a API oficial do object cache e informa corretamente cache hit de valores falsy. */
    public function get(string $key, bool &$found = null)
    {
        $found = false;
        return wp_cache_get($key, $this->group, false, $found);
    }

    /* Persiste um valor no object cache pelo TTL solicitado. */
    public function set(string $key, $value, int $ttl): bool
    {
        return (bool)wp_cache_set($key, $value, $this->group, max(0, $ttl));
    }

    /* Remove uma chave exata do object cache. */
    public function delete(string $key): bool
    {
        return (bool)wp_cache_delete($key, $this->group);
    }

    /* Retorna o identificador do driver para diagnósticos. */
    public function name(): string
    {
        return 'object';
    }
}
