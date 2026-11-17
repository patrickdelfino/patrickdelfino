<?php
namespace AJMotech\Cache;

if (!defined('ABSPATH')) exit;

interface CacheDriverInterface
{
    public function get(string $key, bool &$found = null);
    public function set(string $key, $value, int $ttl): bool;
    public function delete(string $key): bool;
    public function name(): string;
}
