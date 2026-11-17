<?php
/**
 * Cartão de Crédito [ Registro de cartões ]
 *
 * @author Sarthemes <sarthemes@gmail.com>
 * @link https://sarthemes.com/
 * @copyright 2020-2024 Sarthemes
 */

use STCode\MetafieldsPosts;

class Credito {

    private static $instance;
    private function __construct() {
        add_action('init', [$this,'registrar_post_type']);
        add_action('add_meta_boxes', [$this,'adicionar_campos_customizados']);
    }

    public static function get_instance() 
    {
        if ( null == self::$instance ) {
            self::$instance = new Credito();
        }
        return self::$instance;
    }

    public function registrar_post_type() {
        $labels = array(
            'name'               => 'Cartões de Crédito',
            'singular_name'      => 'Cartão de Crédito',
            'menu_name'          => 'Cartões de Crédito',
            'add_new'            => 'Adicionar',
            'add_new_item'       => 'Adicionar Novo Cartão de Crédito',
            'edit_item'          => 'Editar Cartão de Crédito',
            'new_item'           => 'Novo Cartão de Crédito',
            'view_item'          => 'Ver Cartão de Crédito',
            'search_items'       => 'Buscar Cartões de Crédito',
            'not_found'          => 'Nenhum cartão encontrado',
            'not_found_in_trash' => 'Nenhum cartão encontrado na lixeira',
        );

        $args = array(
            'labels'             => $labels,
            'labels'              => $labels,
            'supports'            => array('title','thumbnail'),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 4,
            'menu_icon'           => 'dashicons-stcode-card-post',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => false,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'capability_type'     => 'post',
            'publicly_queryable'  => true,
            'taxonomies'         => array('prós', 'contras'), // Taxonomias para Prós e Contras
        );

        register_post_type('stcode_cartao', $args);
    }

    // Adicionar campos customizados
    public function adicionar_campos_customizados() {
        add_meta_box('campos_cartao_credito','Detalhes',[$this,'mostrar_campos_customizados'],'stcode_cartao','normal','default');

    }

    public function mostrar_campos_customizados($post) {
        // Nonce security
        wp_nonce_field('_cartao_nonce', 'cartao_nonce');
        $opcs = array(
            array(
                'id'     => 'titulo_original',
                'type'   => 'text',
                'label'  => 'Nome do Banco:',
            ),
            array(
                'id'     => 'stcode_cartao_link',
                'type'   => 'text',
                'label'  => 'Cartão Link:',
                'clabel' => 'Qual a anuidade do cartão: ex; Sem Anuidade, 12x 15,99'
            ),
            array(
                'id'     => 'stcode_anuidade',
                'type'   => 'text',
                'label'  => 'Anuidade:',
                'clabel' => 'Qual a anuidade do cartão: ex; Sem Anuidade, 12x 15,99'
            ),
            array(
                'id'     => 'stcode_limite_credito',
                'type'   => 'text',
                'label'  => 'Limite de Crédito:',
                'clabel' => 'Qual o limite de crédito: ex até 5.0000mil'
            ),
            array(
                'id'        => 'stcode_renda_minima',
                'type'      => 'text',
                'label'     => 'Renda Mínima:',
                'clabel'    => 'Qual a renda mínima o cliente deve ter'
            ),
            array(
                'id'        => 'stcode_analise_necessaria',
                'type'      => 'radio',
                'label'     => 'Necessita de análise:',
                'clabel'    => 'O cartão precisa de análise de crédito?',
                'options'   => [
                    '1' => 'Sim',
                    '2' => 'Não'
                ]
            ),
            array(
                'id'        => 'stcode_notas',
                'type'      => 'select',
                'label'     => 'Monify Avaliação:',
                'clabel'    => 'Qual nota para o cartão?',
                'options'   => [
                    '1' => '1+',
                    '1.1' => '1.1+',
                    '1.2' => '1.2+',
                    '1.3' => '1.3+',
                    '1.4' => '1.4+',
                    '1.5' => '1.5+',
                    '2' => '2+',
                    '2.1' => '2.1+',
                    '2.2' => '2.2+',
                    '2.3' => '2.3+',
                    '2.4' => '2.4+',
                    '2.5' => '2.5+',
                    '3' => '3+',
                    '3.1' => '3.1+',
                    '3.2' => '3.2+',
                    '3.3' => '3.3+',
                    '3.4' => '3.4+',
                    '3.5' => '3.5+',
                    '4' => '4+',
                    '4.1' => '4.1+',
                    '4.2' => '4.2+',
                    '4.3' => '4.3+',
                    '4.4' => '4.4+',
                    '4.5' => '4.5+',
                    '5' => '5+'
                ]
            ),
            array(
                'id'        => 'stcode_beneficios_detalhados',
                'type'      => 'textarea',
                'rows'      => 7,
                'label'     => 'Benefícios do cartão:',
            ),
            array(
                'id'        => 'stcode_bandeira_detalhado',
                'type'      => 'textarea',
                'rows'      => 7,
                'label'     => 'Benefícios da bandeira:',
            ),
            array(
                'id'        => 'stcode_tabs',
                'type'      => 'textarea',
                'rows'      => 10,
                'label'     => 'Tabs:',
            ),
        );

        new MetafieldsPosts($opcs);
    }
}
