<?php
namespace AJMotech\Cache;

if (!defined('ABSPATH')) exit;

final class CacheMetrics
{
    private static bool $registered = false;
    private static ?bool $enabled = null;
    private static array $request = array(
        'memory_hits' => 0,
        'driver_hits' => 0,
        'wait_hits' => 0,
        'misses' => 0,
        'sets' => 0,
        'builds' => 0,
        'build_ms' => 0.0,
        'lock_waits' => 0,
        'lock_timeouts' => 0,
        'invalidations' => 0,
        'slow' => array(),
    );

    /* Registra uma única coleta no shutdown, sem gravar métricas em todas as requisições. */
    public static function register(): void
    {
        if (self::$registered) return;
        self::$registered = true;
        add_action('shutdown', array(__CLASS__, 'flush'), 9999);
    }

    /* Registra cache hit distinguindo memória local, driver persistente e resultado obtido durante espera de lock. */
    public static function hit(string $source): void
    {
        if (!self::enabled()) return;
        if ($source === 'memory') self::$request['memory_hits']++;
        elseif ($source === 'wait') self::$request['wait_hits']++;
        else self::$request['driver_hits']++;
    }

    /* Registra cache miss antes da eventual reconstrução protegida por lock. */
    public static function miss(): void
    {
        if (!self::enabled()) return;
        self::$request['misses']++;
    }

    /* Registra uma persistência realizada pelo driver ativo. */
    public static function set(): void
    {
        if (!self::enabled()) return;
        self::$request['sets']++;
    }

    /* Registra disputa de lock e informa quando a espera terminou sem outro processo reconstruir a chave. */
    public static function lockWait(bool $timeout = false): void
    {
        if (!self::enabled()) return;
        self::$request['lock_waits']++;
        if ($timeout) self::$request['lock_timeouts']++;
    }

    /* Registra o tempo do callback executado em cache miss e guarda apenas builds acima do limiar configurado. */
    public static function build(string $group, string $key, float $milliseconds): void
    {
        if (!self::enabled()) return;
        self::$request['builds']++;
        self::$request['build_ms'] += max(0, $milliseconds);
        if ($milliseconds < CacheSettings::slowBuildMs()) return;
        $label = sanitize_key($group) . ':' . substr(preg_replace('/[^a-zA-Z0-9:_\-.]/', '-', $key), 0, 100);
        if (!isset(self::$request['slow'][$label])) self::$request['slow'][$label] = array('group' => sanitize_key($group), 'key' => substr($key, 0, 120), 'count' => 0, 'max_ms' => 0.0, 'total_ms' => 0.0);
        self::$request['slow'][$label]['count']++;
        self::$request['slow'][$label]['max_ms'] = max((float)self::$request['slow'][$label]['max_ms'], $milliseconds);
        self::$request['slow'][$label]['total_ms'] += $milliseconds;
    }

    /* Registra invalidações lógicas para medir churn de tags/grupos sem tocar no banco. */
    public static function invalidation(int $count = 1): void
    {
        if (!self::enabled()) return;
        self::$request['invalidations'] += max(1, $count);
    }

    /* Persiste uma amostra agregada por hora em arquivo pequeno; nenhuma métrica operacional é gravada no MySQL. */
    public static function flush(): void
    {
        if (!self::enabled()) return;
        $rate = CacheSettings::metricsSampleRate();
        if ($rate < 100 && wp_rand(1, 10000) > ($rate * 100)) return;
        $path = self::path();
        if (!self::ensureDirectory($path)) return;
        $file = $path . '/' . gmdate('YmdH') . '.json';
        $handle = @fopen($file, 'c+');
        if (!$handle || !@flock($handle, LOCK_EX)) {
            if ($handle) @fclose($handle);
            return;
        }
        $raw = stream_get_contents($handle);
        $data = $raw ? json_decode($raw, true) : array();
        $data = is_array($data) ? $data : array();
        $data += array('sampled_requests' => 0, 'estimated_requests' => 0.0, 'memory_hits' => 0, 'driver_hits' => 0, 'wait_hits' => 0, 'misses' => 0, 'sets' => 0, 'builds' => 0, 'build_ms' => 0.0, 'lock_waits' => 0, 'lock_timeouts' => 0, 'invalidations' => 0, 'slow' => array());
        $data['sampled_requests']++;
        $data['estimated_requests'] += 100 / max(1, $rate);
        foreach (array('memory_hits', 'driver_hits', 'wait_hits', 'misses', 'sets', 'builds', 'lock_waits', 'lock_timeouts', 'invalidations') as $key) $data[$key] += (int)self::$request[$key];
        $data['build_ms'] += (float)self::$request['build_ms'];
        foreach (self::$request['slow'] as $label => $row) {
            if (!isset($data['slow'][$label])) $data['slow'][$label] = array('group' => $row['group'], 'key' => $row['key'], 'count' => 0, 'max_ms' => 0.0, 'total_ms' => 0.0);
            $data['slow'][$label]['count'] += (int)$row['count'];
            $data['slow'][$label]['max_ms'] = max((float)$data['slow'][$label]['max_ms'], (float)$row['max_ms']);
            $data['slow'][$label]['total_ms'] += (float)$row['total_ms'];
        }
        if (count($data['slow']) > 30) {
            uasort($data['slow'], static function(array $a, array $b): int { return ($b['max_ms'] <=> $a['max_ms']); });
            $data['slow'] = array_slice($data['slow'], 0, 30, true);
        }
        rewind($handle);
        @ftruncate($handle, 0);
        @fwrite($handle, wp_json_encode($data));
        @fflush($handle);
        @flock($handle, LOCK_UN);
        @fclose($handle);
    }

    /* Agrega as últimas horas usando nomes exatos de arquivo, sem varrer recursivamente o diretório de cache. */
    public static function snapshot(int $hours = 24): array
    {
        $hours = max(1, min(168, $hours));
        $sum = array('sampled_requests' => 0, 'estimated_requests' => 0.0, 'memory_hits' => 0, 'driver_hits' => 0, 'wait_hits' => 0, 'misses' => 0, 'sets' => 0, 'builds' => 0, 'build_ms' => 0.0, 'lock_waits' => 0, 'lock_timeouts' => 0, 'invalidations' => 0, 'slow' => array());
        for ($i = 0; $i < $hours; $i++) {
            $file = self::path() . '/' . gmdate('YmdH', time() - ($i * HOUR_IN_SECONDS)) . '.json';
            if (!is_file($file)) continue;
            $row = json_decode((string)@file_get_contents($file), true);
            if (!is_array($row)) continue;
            foreach (array('sampled_requests', 'estimated_requests', 'memory_hits', 'driver_hits', 'wait_hits', 'misses', 'sets', 'builds', 'build_ms', 'lock_waits', 'lock_timeouts', 'invalidations') as $key) $sum[$key] += (float)($row[$key] ?? 0);
            foreach ((array)($row['slow'] ?? array()) as $label => $slow) {
                if (!isset($sum['slow'][$label])) $sum['slow'][$label] = array('group' => (string)($slow['group'] ?? ''), 'key' => (string)($slow['key'] ?? ''), 'count' => 0, 'max_ms' => 0.0, 'total_ms' => 0.0);
                $sum['slow'][$label]['count'] += (int)($slow['count'] ?? 0);
                $sum['slow'][$label]['max_ms'] = max((float)$sum['slow'][$label]['max_ms'], (float)($slow['max_ms'] ?? 0));
                $sum['slow'][$label]['total_ms'] += (float)($slow['total_ms'] ?? 0);
            }
        }
        $requests = (float)$sum['memory_hits'] + (float)$sum['driver_hits'] + (float)$sum['wait_hits'] + (float)$sum['misses'];
        $hits = (float)$sum['memory_hits'] + (float)$sum['driver_hits'] + (float)$sum['wait_hits'];
        $sum['hit_ratio'] = $requests > 0 ? ($hits / $requests) * 100 : 0.0;
        $sum['avg_build_ms'] = (float)$sum['builds'] > 0 ? (float)$sum['build_ms'] / (float)$sum['builds'] : 0.0;
        uasort($sum['slow'], static function(array $a, array $b): int { return ($b['max_ms'] <=> $a['max_ms']); });
        $sum['slow'] = array_slice($sum['slow'], 0, 15, true);
        return $sum;
    }

    /* Remove arquivos horários antigos do diretório de métricas em uma operação administrativa/cron limitada. */
    public static function cleanup(int $days = 7): int
    {
        $deleted = 0;
        $threshold = time() - (max(1, $days) * DAY_IN_SECONDS);
        foreach ((array)glob(self::path() . '/*.json') as $file) {
            if (!is_file($file) || @filemtime($file) >= $threshold) continue;
            if (@unlink($file)) $deleted++;
        }
        return $deleted;
    }

    /* Resolve uma única vez por requisição se a coleta está habilitada. */
    private static function enabled(): bool
    {
        if (self::$enabled === null) self::$enabled = CacheSettings::metricsEnabled();
        return self::$enabled;
    }

    /* Retorna o diretório isolado usado pelas métricas amostradas. */
    private static function path(): string
    {
        return WP_CONTENT_DIR . '/cache/ajm-data-cache-v2/metrics';
    }

    /* Garante o diretório de métricas sem criar dependência com o driver atualmente escolhido. */
    private static function ensureDirectory(string $directory): bool
    {
        if (!is_dir($directory)) {
            if (!function_exists('wp_mkdir_p')) require_once ABSPATH . 'wp-admin/includes/file.php';
            if (!wp_mkdir_p($directory)) return false;
        }
        if (!is_writable($directory)) return false;
        $root = dirname($directory);
        if (!is_file($root . '/index.php')) @file_put_contents($root . '/index.php', "<?php\n// Silence is golden.\n", LOCK_EX);
        if (!is_file($root . '/.htaccess')) @file_put_contents($root . '/.htaccess', "Deny from all\n", LOCK_EX);
        return true;
    }
}
