<?php get_header(); ?>
<div class="container">
	<?php echo stcode_breadcrumbs(); ?>
	<?php
	$min = isset( $_GET['min_price'] ) ? $_GET['min_price'] : 0;
	$max = isset( $_GET['max_price'] ) ? $_GET['max_price'] : 10000;
	?>
	<div class="row">
		<div class="col-12 col-lg-2 left-col">
			<div class="shop__filters-left">
				<div class="filter">
					<div class="filter__category">Tipo de Produto</div>
					<?php
					$tipos_de_produto = get_terms('tipo-produto');
					if ($tipos_de_produto) :
					    foreach ($tipos_de_produto as $tipo) :
					        echo '<div class="filter__rule"><input id="filter__input-' . esc_attr($tipo->term_id) . '" name="tipo-produto" type="checkbox" class="filter__rule-input" data-keynormalizedname="tipo-produto" value="' . esc_attr($tipo->slug) . '"><label for="filter__input-' . esc_attr($tipo->term_id) . '" class="filter__rule-label">' . esc_html($tipo->name) . '</label></div>';
					    endforeach;
					endif;
					?>
				</div>
				<div class="filter">
					<div class="filter__category">Marcas</div>
					<?php
					$marcas = get_terms('marcas');
					if ($marcas) :
					    foreach ($marcas as $marca) :
					        echo '<div class="filter__rule"><input id="filter__input-' . esc_attr($marca->term_id) . '" name="marcas" type="checkbox" class="filter__rule-input" data-keynormalizedname="marcas" value="' . esc_attr($marca->slug) . '"><label for="filter__input-' . esc_attr($marca->term_id) . '" class="filter__rule-label">' . esc_html($marca->name) . '</label></div>';
					    endforeach;
					endif;
					?>
				</div>
				<div class="filter">
					<div class="filter__category">Capacidade de Tiros</div>
					<?php
					$capacidade_tiros = get_terms('capacidade-tiros');
					if ($capacidade_tiros) :
					    foreach ($capacidade_tiros as $capacidade) :
					        echo '<div class="filter__rule"><input id="filter__input-' . esc_attr($capacidade->term_id) . '" name="capacidade-tiros" type="checkbox" class="filter__rule-input" data-keynormalizedname="capacidade-tiros" value="' . esc_attr($capacidade->slug) . '"><label for="filter__input-' . esc_attr($capacidade->term_id) . '" class="filter__rule-label">' . esc_html($capacidade->name) . '</label></div>';
					    endforeach;
					endif;
					?>
				</div>
				<div class="filter">
					<div class="filter__category">Munições</div>
					<?php
					$municoes = get_terms('municoes');
					if ($municoes) :
					    foreach ($municoes as $municao) :
					        echo '<div class="filter__rule"><input id="filter__input-' . esc_attr($municao->term_id) . '" name="municoes" type="checkbox" class="filter__rule-input" data-keynormalizedname="municoes" value="' . esc_attr($municao->slug) . '"><label for="filter__input-' . esc_attr($municao->term_id) . '" class="filter__rule-label">' . esc_html($municao->name) . '</label></div>';
					    endforeach;
					endif;
					?>
				</div>
				<div class="filter">
					<div class="filter__category">Calibre</div>
					<?php
					$calibres = get_terms('calibre');
					if ($calibres) :
					    foreach ($calibres as $calibre) :
					        echo '<div class="filter__rule"><input id="filter__input-' . esc_attr($calibre->term_id) . '" name="calibre" type="checkbox" class="filter__rule-input" data-keynormalizedname="calibre" value="' . esc_attr($calibre->slug) . '"><label for="filter__input-' . esc_attr($calibre->term_id) . '" class="filter__rule-label">' . esc_html($calibre->name) . '</label></div>';
					    endforeach;
					endif;
					?>
				</div>
				<div class="filter">
					<div class="filter__category">Faixa de Preço</div>
					<div class="filter__rule">
						<div class="range_container filter_range_preco" data-class="filter_range_preco">
							<div class="sliders_control">
								<input class="filter_range fromSlider" type="range" value="<?php echo $min; ?>" min="0" max="1000">
								<input class="filter_range toSlider" type="range" value="<?php echo $max; ?>" min="0" max="5000" style="z-index: 2;">
							</div>
							<div class="form_control">
								<div class="form_control_container">
									<span class="form_control_container__time">Mínimo:</span>
									<input class="form_control_container__time__input filter_range fromInput" type="number" value="<?php echo $min; ?>" min="0" max="1000">
								</div>
								<div class="form_control_container">
									<span class="form_control_container__time">Máximo:</span>
									<input class="form_control_container__time__input filter_range toInput" type="number" value="<?php echo $max; ?>" min="0" max="5000">
								</div>
							</div>
							<input type="checkbox" name="_preco-0-1200" class="filters__filter__list__item__checkbox filter_range_checkbox" id="_preco-0-1200" value="0-1200" data-keynormalizedname="_preco" data-name="0-100">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-12 col-lg-10 right-col">
			<div class="overlay">
			    <div class="loading-balls">
			        <div></div>
			        <div></div>
			        <div></div>
			    </div>
			</div>
			<div class="listagem tax-products-itens">
				<div data-produtos-linha="4" class="l-product l-product-four produtos-carrossel">
					<?php
					if ( get_query_var( 'paged' ) ) { $paged = get_query_var( 'paged' ); }
					elseif ( get_query_var( 'page' ) ) { $paged = get_query_var( 'page' ); }
					else { $paged = 1; }

					$args = array(
					    'post_type'      => 'product',
					    'paged'          => $paged,
					    'posts_per_page' => 16,
					    'orderby'        => 'date',
					);

					// Adicione a lógica para incluir taxonomias baseadas nos parâmetros GET
					$tax_query = array();

					$taxonomies = array(
					    'tipo-produto',
					    'marcas',
					    'capacidade-tiros',
					    'municoes',
					    'calibre',
					);

					foreach ($taxonomies as $taxonomy) {
					    if (!empty($_GET[$taxonomy])) {
					        $tax_query[] = array(
					            'taxonomy' => $taxonomy,
					            'field'    => 'slug',
					            'terms'    => explode(',', $_GET[$taxonomy]),
					        );
					    }
					}

					// Adicione a tax_query aos argumentos da consulta principal
					if (!empty($tax_query)) {
					    $args['tax_query'] = $tax_query;
					}

					$args['meta_query'] = array(
				        array(
				            'key' => '_price',
				            'value' => array($min, $max), // Faixa de preços (mínimo e máximo)
				            'type' => 'NUMERIC',
				            'compare' => 'BETWEEN',
				        ),
				    );

					// Continue com o seu loop principal
					$loop = new WP_Query($args);

					if ($loop->have_posts()) :
					    while ($loop->have_posts()) : $loop->the_post();
					    	get_template_part('inc/parts/product');
					    endwhile;
					endif;

					// Restaura os dados originais do post
					wp_reset_postdata();
					?>
				</div>
				<?php pagination($loop->max_num_pages); ?>
			</div>
		</div>
	</div>
</div>
<?php get_footer(); ?>