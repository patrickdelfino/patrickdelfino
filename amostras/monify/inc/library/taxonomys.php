<?php
/**
 * Taxonomys [ Registro de Categorias ]
 *
 * @author Sarthemes <sarthemes@gmail.com>
 * @link https://sarthemes.com/
 * @copyright 2020-2024 Sarthemes
 */

class Taxonomy {

    private static $instance;
    private function __construct() {
        add_action('init', [$this,'taxonomy_contras']);
        add_action('init', [$this,'taxonomy_pros']);
        add_action('init', [$this,'taxonomy_perfil']);
        add_action('init', [$this,'taxonomy_bandeiras']);
        add_action('init', [$this,'taxonomy_beneficios']);
        add_action('init', [$this,'taxonomy_categorias']);
    }

    public static function get_instance() 
    {
        if ( null == self::$instance ) {
            self::$instance = new Taxonomy();
        }
        return self::$instance;
    }

    /**
     * Registra a taxonomia 'Prós' para os tipos de postagem 'stcode_cartao', 'stcode_conta_digital' e 'stcode_emprestimo'.
     */
    public function taxonomy_pros() {

        register_taxonomy( 'pros', ['stcode_cartao','stcode_conta_digital','stcode_emprestimo'], array( 
            'hierarchical' => true,
            'labels'            => array(
                'name'                       => _x( 'Prós', 'Taxonomy General Name', STCODE_OPTIONS ),
                'singular_name'              => _x( 'Prós', 'Taxonomy Singular Name', STCODE_OPTIONS ),
                'menu_name'                  => __( 'Prós', STCODE_OPTIONS ),
                'all_items'                  => __( 'Todos os prós', STCODE_OPTIONS ),
                'parent_item'                => __( 'Prós Vinculados', STCODE_OPTIONS ),
                'parent_item_colon'          => __( 'Prós Vinculados', STCODE_OPTIONS ),
                'new_item_name'              => __( 'Criar um pró', STCODE_OPTIONS ),
                'add_new_item'               => __( 'Adicionar um pró', STCODE_OPTIONS ),
                'edit_item'                  => __( 'Editar pró', STCODE_OPTIONS ),
                'update_item'                => __( 'Atualizar pró', STCODE_OPTIONS ),
                'view_item'                  => __( 'Ver pró', STCODE_OPTIONS ),
                'separate_items_with_commas' => __( 'Separar os prós com virgulas', STCODE_OPTIONS ),
                'add_or_remove_items'        => __( 'Adicionar ou Remover prós', STCODE_OPTIONS ),
                'choose_from_most_used'      => __( 'Os Mais Usados', STCODE_OPTIONS ),
                'popular_items'              => __( 'Prós Populares', STCODE_OPTIONS ),
                'search_items'               => __( 'Procurar Prós', STCODE_OPTIONS ),
                'not_found'                  => __( 'Não Encontrado', STCODE_OPTIONS ),
                'no_terms'                   => __( 'Sem Pró', STCODE_OPTIONS ),
                'items_list'                 => __( 'Lista de Prós', STCODE_OPTIONS ),
                'items_list_navigation'      => __( 'Navegação', STCODE_OPTIONS ),
            ))
        );
    }

    /**
     * Registra a taxonomia 'Contras' para os tipos de postagem 'stcode_cartao', 'stcode_conta_digital' e 'stcode_emprestimo'.
     */
    public function taxonomy_contras() {

        register_taxonomy( 'contras', ['stcode_cartao','stcode_conta_digital','stcode_emprestimo'], array( 
            'hierarchical' => true,
            'labels'            => array(
                'name'                       => _x( 'Contras', 'Taxonomy General Name', STCODE_OPTIONS ),
                'singular_name'              => _x( 'Contras', 'Taxonomy Singular Name', STCODE_OPTIONS ),
                'menu_name'                  => __( 'Contras', STCODE_OPTIONS ),
                'all_items'                  => __( 'Todos os contras', STCODE_OPTIONS ),
                'parent_item'                => __( 'Contras Vinculados', STCODE_OPTIONS ),
                'parent_item_colon'          => __( 'Contras Vinculados', STCODE_OPTIONS ),
                'new_item_name'              => __( 'Criar um contra', STCODE_OPTIONS ),
                'add_new_item'               => __( 'Adicionar um contra', STCODE_OPTIONS ),
                'edit_item'                  => __( 'Editar contra', STCODE_OPTIONS ),
                'update_item'                => __( 'Atualizar contra', STCODE_OPTIONS ),
                'view_item'                  => __( 'Ver contra', STCODE_OPTIONS ),
                'separate_items_with_commas' => __( 'Separar os contras com virgulas', STCODE_OPTIONS ),
                'add_or_remove_items'        => __( 'Adicionar ou Remover contras', STCODE_OPTIONS ),
                'choose_from_most_used'      => __( 'Os Mais Usados', STCODE_OPTIONS ),
                'popular_items'              => __( 'Contras Populares', STCODE_OPTIONS ),
                'search_items'               => __( 'Procurar Contras', STCODE_OPTIONS ),
                'not_found'                  => __( 'Não Encontrado', STCODE_OPTIONS ),
                'no_terms'                   => __( 'Sem Contra', STCODE_OPTIONS ),
                'items_list'                 => __( 'Lista de Contras', STCODE_OPTIONS ),
                'items_list_navigation'      => __( 'Navegação', STCODE_OPTIONS ),
            ))
        );

    }

    /**
     * Registra a taxonomia 'Perfil Ideal' para os tipos de postagem 'stcode_cartao' e 'stcode_emprestimo'.
     */
    public function taxonomy_perfil() {

        register_taxonomy( 'perfil_ideal', ['stcode_cartao','stcode_emprestimo'], array( 
            'hierarchical' => true,
            'labels'            => array(
                'name'                       => _x( 'Perfil Ideal', 'Taxonomy General Name', STCODE_OPTIONS ),
                'singular_name'              => _x( 'Perfil Ideal', 'Taxonomy Singular Name', STCODE_OPTIONS ),
                'menu_name'                  => __( 'Perfil Ideal', STCODE_OPTIONS ),
                'all_items'                  => __( 'Todos os perfils', STCODE_OPTIONS ),
                'parent_item'                => __( 'Perfils ideais Vinculados', STCODE_OPTIONS ),
                'parent_item_colon'          => __( 'Perfils ideais Vinculados', STCODE_OPTIONS ),
                'new_item_name'              => __( 'Criar um perfil', STCODE_OPTIONS ),
                'add_new_item'               => __( 'Adicionar um perfil', STCODE_OPTIONS ),
                'edit_item'                  => __( 'Editar perfil', STCODE_OPTIONS ),
                'update_item'                => __( 'Atualizar perfil', STCODE_OPTIONS ),
                'view_item'                  => __( 'Ver perfil', STCODE_OPTIONS ),
                'separate_items_with_commas' => __( 'Separar os perfils com virgulas', STCODE_OPTIONS ),
                'add_or_remove_items'        => __( 'Adicionar ou Remover perfils', STCODE_OPTIONS ),
                'choose_from_most_used'      => __( 'Os Mais Usados', STCODE_OPTIONS ),
                'popular_items'              => __( 'Perfils Populares', STCODE_OPTIONS ),
                'search_items'               => __( 'Procurar Perfils', STCODE_OPTIONS ),
                'not_found'                  => __( 'Não Encontrado', STCODE_OPTIONS ),
                'no_terms'                   => __( 'Sem Perfil', STCODE_OPTIONS ),
                'items_list'                 => __( 'Lista de Perfils', STCODE_OPTIONS ),
                'items_list_navigation'      => __( 'Navegação', STCODE_OPTIONS ),
            ))
        );

    }

    /**
     * Registra a taxonomia 'Documentos necessários' para os tipos de postagem 'stcode_conta_digital' e 'stcode_emprestimo'.
     */
    public function taxonomy_documentos() {

        register_taxonomy( 'documentos', ['stcode_conta_digital','stcode_emprestimo'], array( 
            'hierarchical' => true,
            'labels'            => array(
                'name'                       => _x( 'Documentos necessários', 'Taxonomy General Name', STCODE_OPTIONS ),
                'singular_name'              => _x( 'Documentos necessários', 'Taxonomy Singular Name', STCODE_OPTIONS ),
                'menu_name'                  => __( 'Documentos necessários', STCODE_OPTIONS ),
                'all_items'                  => __( 'Todos os documentos necessários', STCODE_OPTIONS ),
                'parent_item'                => __( 'Documentos necessários vinculados', STCODE_OPTIONS ),
                'parent_item_colon'          => __( 'Documentos necessários vinculados', STCODE_OPTIONS ),
                'new_item_name'              => __( 'Criar um documento necessário', STCODE_OPTIONS ),
                'add_new_item'               => __( 'Adicionar um documento', STCODE_OPTIONS ),
                'edit_item'                  => __( 'Editar documento', STCODE_OPTIONS ),
                'update_item'                => __( 'Atualizar documento', STCODE_OPTIONS ),
                'view_item'                  => __( 'Ver documento', STCODE_OPTIONS ),
                'separate_items_with_commas' => __( 'Separar os documentos com virgulas', STCODE_OPTIONS ),
                'add_or_remove_items'        => __( 'Adicionar ou Remover documentos', STCODE_OPTIONS ),
                'choose_from_most_used'      => __( 'Os Mais Usados', STCODE_OPTIONS ),
                'popular_items'              => __( 'Documentos Populares', STCODE_OPTIONS ),
                'search_items'               => __( 'Procurar Documentos', STCODE_OPTIONS ),
                'not_found'                  => __( 'Não Encontrado', STCODE_OPTIONS ),
                'no_terms'                   => __( 'Sem Perfil', STCODE_OPTIONS ),
                'items_list'                 => __( 'Lista de Documentos necessários', STCODE_OPTIONS ),
                'items_list_navigation'      => __( 'Navegação', STCODE_OPTIONS ),
            ))
        );

    }

    /**
     * Registra a taxonomia 'Benefícios' para o tipo de postagem 'stcode_cartao'.
     */
    public function taxonomy_beneficios() {

        register_taxonomy( 'stcode_beneficios', ['stcode_conta_digital','stcode_cartao'], array( 
            'hierarchical' => true,
            'labels'            => array(
                'name'                       => _x( 'Benefícios', 'Taxonomy General Name', STCODE_OPTIONS ),
                'singular_name'              => _x( 'Benefícios', 'Taxonomy Singular Name', STCODE_OPTIONS ),
                'menu_name'                  => __( 'Benefícios', STCODE_OPTIONS ),
                'all_items'                  => __( 'Todos os benefícios', STCODE_OPTIONS ),
                'parent_item'                => __( 'Benefícios vinculados', STCODE_OPTIONS ),
                'parent_item_colon'          => __( 'Benefícios vinculados', STCODE_OPTIONS ),
                'new_item_name'              => __( 'Criar um benefício', STCODE_OPTIONS ),
                'add_new_item'               => __( 'Adicionar um benefício', STCODE_OPTIONS ),
                'edit_item'                  => __( 'Editar benefício', STCODE_OPTIONS ),
                'update_item'                => __( 'Atualizar', STCODE_OPTIONS ),
                'view_item'                  => __( 'Ver benefício', STCODE_OPTIONS ),
                'separate_items_with_commas' => __( 'Separar os benefícios com virgulas', STCODE_OPTIONS ),
                'add_or_remove_items'        => __( 'Adicionar ou Remover', STCODE_OPTIONS ),
                'choose_from_most_used'      => __( 'Os Mais Usados', STCODE_OPTIONS ),
                'popular_items'              => __( 'Benefícios Populares', STCODE_OPTIONS ),
                'search_items'               => __( 'Procurar Benefícios', STCODE_OPTIONS ),
                'not_found'                  => __( 'Não Encontrado', STCODE_OPTIONS ),
                'no_terms'                   => __( 'Sem Perfil', STCODE_OPTIONS ),
                'items_list'                 => __( 'Lista de Benefícios', STCODE_OPTIONS ),
                'items_list_navigation'      => __( 'Navegação', STCODE_OPTIONS ),
            ))
        );

    }

    /**
     * Registra a taxonomia 'Bandeiras' para o tipo de postagem 'stcode_cartao'.
     */
    public function taxonomy_bandeiras() {

        register_taxonomy( 'stcode_bandeiras', ['stcode_cartao'], array( 
            'hierarchical' => true,
            'labels'            => array(
                'name'                       => _x( 'Bandeiras', 'Taxonomy General Name', STCODE_OPTIONS ),
                'singular_name'              => _x( 'Bandeiras', 'Taxonomy Singular Name', STCODE_OPTIONS ),
                'menu_name'                  => __( 'Bandeiras', STCODE_OPTIONS ),
                'all_items'                  => __( 'Todas as bandeiras', STCODE_OPTIONS ),
                'parent_item'                => __( 'Bandeiras vinculadas', STCODE_OPTIONS ),
                'parent_item_colon'          => __( 'Bandeiras vinculadas', STCODE_OPTIONS ),
                'new_item_name'              => __( 'Criar uma bandeira', STCODE_OPTIONS ),
                'add_new_item'               => __( 'Adicionar', STCODE_OPTIONS ),
                'edit_item'                  => __( 'Editar bandeira', STCODE_OPTIONS ),
                'update_item'                => __( 'Atualizar bandeira', STCODE_OPTIONS ),
                'view_item'                  => __( 'Ver bandeira', STCODE_OPTIONS ),
                'separate_items_with_commas' => __( 'Separar as bandeiras com virgulas', STCODE_OPTIONS ),
                'add_or_remove_items'        => __( 'Adicionar ou Remover', STCODE_OPTIONS ),
                'choose_from_most_used'      => __( 'As Mais Usadas', STCODE_OPTIONS ),
                'popular_items'              => __( 'Bandeiras Populares', STCODE_OPTIONS ),
                'search_items'               => __( 'Procurar Bandeiras', STCODE_OPTIONS ),
                'not_found'                  => __( 'Não Encontrado', STCODE_OPTIONS ),
                'no_terms'                   => __( 'Sem Perfil', STCODE_OPTIONS ),
                'items_list'                 => __( 'Lista de bandeiras', STCODE_OPTIONS ),
                'items_list_navigation'      => __( 'Navegação', STCODE_OPTIONS ),
            ))
        );

    }

    /**
     * Registra a taxonomia 'Categoria' para o tipo de postagem 'stcode_cartao'.
     */
    public function taxonomy_categorias() {

        register_taxonomy( 'stcode_categoria', ['stcode_cartao'], array( 
            'hierarchical' => true,
            'labels'            => array(
                'name'                       => _x( 'Categoria do cartão', 'Taxonomy General Name', STCODE_OPTIONS ),
                'singular_name'              => _x( 'Categoria do cartão', 'Taxonomy Singular Name', STCODE_OPTIONS ),
                'menu_name'                  => __( 'Categoria do cartão', STCODE_OPTIONS ),
                'all_items'                  => __( 'Todas as categorias', STCODE_OPTIONS ),
                'parent_item'                => __( 'Categorias vinculadas', STCODE_OPTIONS ),
                'parent_item_colon'          => __( 'Categorias vinculadas', STCODE_OPTIONS ),
                'new_item_name'              => __( 'Criar uma categoria', STCODE_OPTIONS ),
                'add_new_item'               => __( 'Adicionar', STCODE_OPTIONS ),
                'edit_item'                  => __( 'Editar categoria', STCODE_OPTIONS ),
                'update_item'                => __( 'Atualizar categoria', STCODE_OPTIONS ),
                'view_item'                  => __( 'Ver categoria', STCODE_OPTIONS ),
                'separate_items_with_commas' => __( 'Separar as categorias com virgulas', STCODE_OPTIONS ),
                'add_or_remove_items'        => __( 'Adicionar ou Remover', STCODE_OPTIONS ),
                'choose_from_most_used'      => __( 'As Mais Usadas', STCODE_OPTIONS ),
                'popular_items'              => __( 'Categorias Populares', STCODE_OPTIONS ),
                'search_items'               => __( 'Procurar categorias', STCODE_OPTIONS ),
                'not_found'                  => __( 'Não Encontrado', STCODE_OPTIONS ),
                'no_terms'                   => __( 'Sem Categoria', STCODE_OPTIONS ),
                'items_list'                 => __( 'Lista de categorias', STCODE_OPTIONS ),
                'items_list_navigation'      => __( 'Navegação', STCODE_OPTIONS ),
            ))
        );

    }
}

Taxonomy::get_instance();