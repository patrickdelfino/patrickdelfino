<?php
/**
 * Classe Sitemap para CMS Próprio
 *
 * Esta classe é responsável por gerar sitemaps dinâmicos para o CMS próprio, 
 * suportando os formatos XML, HTML e suas versões comprimidas (xml.gz, html.gz).
 *
 * São incluídas as seguintes tabelas do sistema (conforme migrate.php): 
 * livetvs, animes, filmes, temporadas, series, episodios e generos.
 *
 * O sistema permite:
 * - Sitemap global: Lista os sitemaps paginados para cada tipo de conteúdo.
 * - Sitemap para cada tabela (conteúdo): Paginação dos registros conforme um limite definido.
 * - Sitemap misc: Inclui a homepage e a versão HTML do sitemap.
 *
 * Basta integrar esta classe ao seu sistema de rotas para que as URLs correspondentes sejam processadas.
 *
 * @since 1.0.0
 * @package STCode
 */

namespace STCode;

class Sitemap {
    /**
     * Conexão com o banco de dados (deve ser um objeto PDO ou similar)
     * @var \PDO
     */
    private $db;
    
    /**
     * Limite de itens por página no sitemap
     * @var int
     */
    private $limit;
    
    /**
     * Lista dos tipos de conteúdo (nomes das tabelas) a serem incluídos no sitemap
     * @var array
     */
    private $content_types = [
        'livetvs',
        'animes',
        'filmes',
        'temporadas',
        'series',
        'episodios',
        'generos'
    ];
    
    /**
     * Construtor
     *
     * @param \PDO $db Conexão com o banco de dados
     * @param int $limit Número de registros por página (default 100)
     */
    public function __construct($db, int $limit = 100) {
        $this->db = $db;
        $this->limit = $limit;
    }
    
    /**
     * Trata a requisição do sitemap.
     *
     * Espera parâmetros via GET:
     * - sitemap_type: tipo de sitemap (ex.: global, animes, filmes, etc. ou misc)
     * - sitemap_page: número da página (default 1)
     * - sitemap_format: xml, html, xml.gz ou html.gz (default xml)
     */
    public function handleRequest() {
        $type = isset($_GET['sitemap_type']) ? $_GET['sitemap_type'] : false;
        $page = isset($_GET['sitemap_page']) ? max(1, (int)$_GET['sitemap_page']) : 1;
        $format = isset($_GET['sitemap_format']) ? $_GET['sitemap_format'] : 'xml';
        
        if (!in_array($format, ['xml', 'html', 'xml.gz', 'html.gz'])) {
            $this->send_404();
            return;
        }
        
        if (!$type) {
            // Se não especificado, gera o sitemap global
            $this->generate_global_sitemap($format);
        } elseif ($type === 'misc') {
            $this->generate_misc_sitemap($format);
        } elseif (in_array($type, $this->content_types)) {
            $this->generate_content_sitemap($type, $page, $format);
        } else {
            $this->send_404();
        }
    }
    
    /**
     * Envia resposta 404
     */
    private function send_404() {
        header("HTTP/1.1 404 Not Found");
        echo "404 Not Found";
    }
    
    /**
     * Gera o sitemap global, listando os sitemaps paginados para cada tipo de conteúdo.
     *
     * @param string $format Formato de saída (xml, html, xml.gz, html.gz)
     */
    private function generate_global_sitemap($format) {
        $sitemap = [];
        
        // Adiciona a entrada do sitemap misc
        $sitemap[] = [
            'loc' => SITE_URL . "/sitemap-misc.xml",
            'lastmod' => date('c'),
        ];
        
        // Para cada tabela, calcula o total de registros e cria links paginados
        foreach ($this->content_types as $type) {
            $total = $this->count_records($type);
            $pages = ceil($total / $this->limit);
            for ($i = 1; $i <= $pages; $i++) {
                $sitemap[] = [
                    'loc' => SITE_URL . "/sitemap-{$type}-{$i}.xml",
                    'lastmod' => date('c'),
                ];
            }
        }
        
        $this->output_sitemap($sitemap, $format);
    }
    
    /**
     * Conta o número total de registros de uma tabela.
     *
     * @param string $table Nome da tabela
     * @return int Total de registros
     */
    private function count_records($table) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM `$table`");
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? (int)$row['total'] : 0;
    }
    
    /**
     * Gera o sitemap para um tipo de conteúdo (tabela) específico.
     *
     * @param string $type Nome da tabela (ex.: animes)
     * @param int $page Número da página
     * @param string $format Formato de saída
     */
    private function generate_content_sitemap($type, $page, $format) {
        $offset = ($page - 1) * $this->limit;
        $stmt = $this->db->prepare("SELECT * FROM `$type` LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $this->limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if (!$items) {
            $this->send_404();
            return;
        }
        
        $sitemap = $this->build_content_sitemap($items, $type);
        $this->output_sitemap($sitemap, $format);
    }
    
    /**
     * Monta as entradas do sitemap para os registros obtidos de uma tabela.
     *
     * @param array $items Registros da tabela
     * @param string $type Nome da tabela
     * @return array Array de entradas para o sitemap
     */
    private function build_content_sitemap($items, $type) {
        $sitemap = [];
        // Frequência e prioridade padrão (podem ser adaptados conforme o conteúdo)
        $frequency = 'weekly';
        $priority = '0.8';
        
        foreach ($items as $item) {
            $url = $this->get_item_url($type, $item);
            $lastmod = isset($item['updated_at']) && $item['updated_at'] 
                        ? $item['updated_at'] 
                        : date('c');
            $sitemap[] = [
                'loc' => $url,
                'lastmod' => $lastmod,
                'changefreq' => $frequency,
                'priority' => $priority,
            ];
        }
        
        return $sitemap;
    }
    
    /**
     * Retorna a URL de um item, utilizando o campo "slug" se disponível, ou o "id".
     *
     * @param string $type Nome da tabela
     * @param array $item Registro do banco de dados
     * @return string URL completa do item
     */
    private function get_item_url($type, $item) {
        $slug = (isset($item['slug']) && $item['slug']) ? $item['slug'] : $item['id'];
        return SITE_URL . "/{$type}/{$slug}";
    }
    
    /**
     * Gera o sitemap misc, que pode conter páginas estáticas como a homepage e a versão HTML do sitemap.
     *
     * @param string $format Formato de saída
     */
    private function generate_misc_sitemap($format) {
        $sitemap = [];
        $sitemap[] = [
            'loc' => SITE_URL . '/',
            'lastmod' => date('c'),
            'changefreq' => 'always',
            'priority' => '1.0'
        ];
        $sitemap[] = [
            'loc' => SITE_URL . '/sitemap.html',
            'lastmod' => date('c'),
            'changefreq' => 'monthly',
            'priority' => '0.5'
        ];
        
        $this->output_sitemap($sitemap, $format);
    }
    
    /**
     * Define os cabeçalhos HTTP e exibe o sitemap no formato solicitado.
     *
     * @param array $sitemap Array de entradas do sitemap
     * @param string $format Formato de saída
     */
    private function output_sitemap($sitemap, $format) {
        if (in_array($format, ['xml', 'xml.gz'])) {
            header('Content-Type: application/xml; charset=utf-8');
            $output = $this->convert_to_xml($sitemap);
        } else {
            header('Content-Type: text/html; charset=utf-8');
            $output = $this->convert_to_html($sitemap);
        }
        
        if (substr($format, -3) === '.gz') {
            $output = gzencode($output);
            header('Content-Encoding: gzip');
        }
        
        echo $output;
    }
    
    /**
     * Converte o array de entradas em XML conforme o padrão sitemaps.org.
     *
     * @param array $sitemap Array de entradas
     * @return string XML gerado
     */
    private function convert_to_xml($sitemap) {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
        foreach ($sitemap as $entry) {
            $url = $xml->addChild('url');
            if (isset($entry['loc'])) {
                $url->addChild('loc', htmlspecialchars($entry['loc']));
            }
            if (isset($entry['lastmod'])) {
                $url->addChild('lastmod', $entry['lastmod']);
            }
            if (isset($entry['changefreq'])) {
                $url->addChild('changefreq', $entry['changefreq']);
            }
            if (isset($entry['priority'])) {
                $url->addChild('priority', $entry['priority']);
            }
        }
        return $xml->asXML();
    }
    
    /**
     * Converte o array de entradas em uma página HTML amigável.
     *
     * @param array $sitemap Array de entradas
     * @return string HTML gerado
     */
    private function convert_to_html($sitemap) {
        $html = '<!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
        $html .= '<title>Sitemap</title>';
        $html .= '<style>body{font-family:Helvetica,Arial,sans-serif;font-size:13px;color:#545353;margin:0}';
        $html .= 'table{border-collapse:collapse;width:100%}tr:nth-child(odd) td{background-color:#f2f7f6!important}';
        $html .= 'tbody tr:hover td{background-color:#dee1e0}tbody tr:hover td a{color:#000}';
        $html .= '#content{margin:0 auto;width:1200px}td,th{padding:10px;text-align:left}';
        $html .= 'thead th{border-bottom:1px solid #000}</style></head><body>';
        $html .= '<div id="content"><h1>Sitemap</h1>';
        $html .= '<table cellpadding="3"><thead><tr>';
        if (isset($sitemap[0]['loc'])) {
            $html .= '<th width="80%">URL</th>';
        }
        if (isset($sitemap[0]['lastmod'])) {
            $html .= '<th width="15%">Last Modified</th>';
        }
        if (isset($sitemap[0]['changefreq'])) {
            $html .= '<th>Frequency</th>';
        }
        if (isset($sitemap[0]['priority'])) {
            $html .= '<th>Priority</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($sitemap as $entry) {
            $html .= '<tr>';
            // Para a versão HTML, pode-se substituir a extensão .xml por .html
            $permalink = str_replace('.xml', '.html', $entry['loc']);
            if (isset($entry['loc'])) {
                $html .= "<td><a href='{$permalink}' target='_blank'>{$permalink}</a></td>";
            }
            if (isset($entry['lastmod'])) {
                $html .= "<td>{$entry['lastmod']}</td>";
            }
            if (isset($entry['changefreq'])) {
                $html .= "<td>{$entry['changefreq']}</td>";
            }
            if (isset($entry['priority'])) {
                $html .= "<td>{$entry['priority']}</td>";
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table></div></body></html>';
        return $html;
    }
}