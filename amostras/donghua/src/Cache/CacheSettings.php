<?php
namespace AJMotech\Cache;

if (!defined('ABSPATH')) exit;

final class CacheSettings
{
    private static bool $codestarRegistered = false;

    /* Lê uma configuração do cache salva na option principal do Codestar. */
    public static function get(string $key, $default = null)
    {
        if (!defined('AJM_OPTIONS')) return $default;
        $options = get_option(AJM_OPTIONS, array());
        return is_array($options) && array_key_exists($key, $options) ? $options[$key] : $default;
    }

    /* Informa se o cache de aplicação está habilitado. */
    public static function enabled(): bool
    {
        return (bool)self::get('cache_v2_enabled', true);
    }

    /* Retorna o driver configurado, mantendo auto como padrão seguro. */
    public static function driver(): string
    {
        $driver = sanitize_key((string)self::get('cache_v2_driver', 'auto'));
        return in_array($driver, array('auto', 'object', 'file'), true) ? $driver : 'auto';
    }

    /* Retorna o TTL padrão limitado para impedir valores acidentais extremos. */
    public static function defaultTtl(): int
    {
        return max(30, min(DAY_IN_SECONDS, absint(self::get('cache_v2_default_ttl', 600))));
    }

    /* Retorna a variação aleatória aplicada ao TTL para impedir expiração simultânea de muitas chaves. */
    public static function ttlJitterPercent(): int
    {
        return max(0, min(25, absint(self::get('cache_v2_ttl_jitter', 10))));
    }

    /* Retorna o tempo máximo de espera por outro processo que esteja reconstruindo a mesma chave. */
    public static function lockWaitMs(): int
    {
        return max(0, min(1500, absint(self::get('cache_v2_lock_wait_ms', 350))));
    }

    /* Retorna a retenção física dos arquivos obsoletos em dias. */
    public static function retentionDays(): int
    {
        return max(1, min(30, absint(self::get('cache_v2_retention_days', 7))));
    }

    /* Retorna o tamanho máximo de cada lote do coletor de lixo. */
    public static function cleanupBatch(): int
    {
        return max(50, min(5000, absint(self::get('cache_v2_cleanup_batch', 500))));
    }

    /* Informa se os atalhos administrativos devem aparecer na barra do WordPress. */
    public static function adminBar(): bool
    {
        return (bool)self::get('cache_v2_admin_bar', true);
    }

    /* Informa se logs de diagnóstico do cache devem ser gravados no error_log. */
    public static function debug(): bool
    {
        return (bool)self::get('cache_v2_debug', false);
    }

    /* Informa se métricas amostradas de cache devem ser coletadas fora do MySQL. */
    public static function metricsEnabled(): bool
    {
        return (bool)self::get('cache_v2_metrics_enabled', true);
    }

    /* Retorna a porcentagem de requisições amostradas para métricas de operação. */
    public static function metricsSampleRate(): int
    {
        return max(1, min(100, absint(self::get('cache_v2_metrics_sample_rate', 2))));
    }

    /* Retorna o limiar em milissegundos usado para destacar reconstruções lentas de cache. */
    public static function slowBuildMs(): int
    {
        return max(25, min(5000, absint(self::get('cache_v2_slow_build_ms', 150))));
    }

    /* Registra a seção Cache dentro de Infraestrutura no Codestar do tema. */
    public static function registerCodestar(): void
    {
        if (self::$codestarRegistered || !class_exists('CSF') || !defined('AJM_OPTIONS')) return;
        self::$codestarRegistered = true;
        self::ensureInfrastructureSection();
        \CSF::createSection(AJM_OPTIONS, array(
            'parent' => 'ajm_infrastructure',
            'title' => 'Cache',
            'fields' => array(
                array('type' => 'subheading', 'content' => 'Cache de aplicação e dados'),
                array('type' => 'notice', 'style' => 'info', 'content' => 'Este módulo acelera consultas, rankings, Home e dados internos. Ele não substitui o cache HTML do WP Rocket, servidor ou Cloudflare.'),
                array('id' => 'cache_v2_enabled', 'type' => 'switcher', 'title' => 'Habilitar cache de aplicação', 'default' => true),
                array('id' => 'cache_v2_driver', 'type' => 'select', 'title' => 'Driver', 'default' => 'auto', 'options' => array('auto' => 'Automático', 'object' => 'Object Cache persistente (fallback arquivo)', 'file' => 'Arquivos'), 'dependency' => array('cache_v2_enabled', '==', 'true')),
                array('id' => 'cache_v2_default_ttl', 'type' => 'number', 'title' => 'TTL padrão', 'default' => 600, 'min' => 30, 'max' => DAY_IN_SECONDS, 'unit' => 'segundos', 'dependency' => array('cache_v2_enabled', '==', 'true')),
                array('id' => 'cache_v2_ttl_jitter', 'type' => 'number', 'title' => 'Jitter do TTL', 'subtitle' => 'Espalha expirações para evitar picos simultâneos de reconstrução.', 'default' => 10, 'min' => 0, 'max' => 25, 'unit' => '%', 'dependency' => array('cache_v2_enabled', '==', 'true')),
                array('id' => 'cache_v2_lock_wait_ms', 'type' => 'number', 'title' => 'Espera anti-stampede', 'subtitle' => 'Tempo máximo que uma requisição aguarda outra reconstruir a mesma chave.', 'default' => 350, 'min' => 0, 'max' => 1500, 'unit' => 'ms', 'dependency' => array('cache_v2_enabled', '==', 'true')),
                array('id' => 'cache_v2_retention_days', 'type' => 'number', 'title' => 'Retenção de arquivos obsoletos', 'default' => 7, 'min' => 1, 'max' => 30, 'unit' => 'dias', 'dependency' => array('cache_v2_enabled', '==', 'true')),
                array('id' => 'cache_v2_cleanup_batch', 'type' => 'number', 'title' => 'Lote da limpeza agendada', 'default' => 500, 'min' => 50, 'max' => 5000, 'dependency' => array('cache_v2_enabled', '==', 'true')),
                array('type' => 'subheading', 'content' => 'Observabilidade do cache'),
                array('id' => 'cache_v2_metrics_enabled', 'type' => 'switcher', 'title' => 'Coletar métricas amostradas', 'subtitle' => 'As métricas são gravadas em arquivos pequenos, não em wp_options.', 'default' => true),
                array('id' => 'cache_v2_metrics_sample_rate', 'type' => 'number', 'title' => 'Taxa de amostragem', 'default' => 2, 'min' => 1, 'max' => 100, 'unit' => '%', 'dependency' => array('cache_v2_metrics_enabled', '==', 'true')),
                array('id' => 'cache_v2_slow_build_ms', 'type' => 'number', 'title' => 'Build lento a partir de', 'default' => 150, 'min' => 25, 'max' => 5000, 'unit' => 'ms', 'dependency' => array('cache_v2_metrics_enabled', '==', 'true')),
                array('id' => 'cache_v2_admin_bar', 'type' => 'switcher', 'title' => 'Atalhos na barra administrativa', 'default' => true),
                array('id' => 'cache_v2_debug', 'type' => 'switcher', 'title' => 'Log de diagnóstico', 'default' => false),
            ),
        ));
    }

    /* Cria uma única raiz Infraestrutura para Cache e Sitemap compartilharem no Codestar. */
    public static function ensureInfrastructureSection(): void
    {
        if (defined('AJM_INFRASTRUCTURE_CODESTAR_REGISTERED')) return;
        define('AJM_INFRASTRUCTURE_CODESTAR_REGISTERED', true);
        \CSF::createSection(AJM_OPTIONS, array('id' => 'ajm_infrastructure', 'title' => 'Infraestrutura', 'icon' => 'fa-solid fa-server'));
    }
}
