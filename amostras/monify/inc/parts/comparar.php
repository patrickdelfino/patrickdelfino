<?php get_header(); ?>
<?php
$cartao1 = 0;
$cartao2 = 0;
if (isset($_GET['cartoes'])) {
    // Obtém o valor do parâmetro "cartoes"
    $cartoes = $_GET['cartoes'];
    
    // Separa os valores usando o caractere "X"
    $cartoes_array = explode('X', $cartoes);

    $cartao1 = get_page_by_path($cartoes_array[0], OBJECT, 'stcode_cartao')->ID;
    $cartao2 = get_page_by_path($cartoes_array[1], OBJECT, 'stcode_cartao')->ID;
}
?>
<a class="prev-tool" href="<?php echo get_home_url(); ?>/comparador-de-cartao-de-credito"><i class='bx bx-chevron-left'></i> retornar à lista de cartões</a>
<div class="container-section pd-fix">
	<div class="h__headline h__headline--fix  text-center">
		<h1>Compare os cartões de crédito</h1>
		<p>Selecione abaixo os cartões que você deseja comparar</p>
	</div>
	<div class="row-header card-sticky">
		<div class="grid-x grid-padding-x">
			<div class="group-section">
				<div class="comparar-page">
					<div class="grid-x grid-padding-x">
						<div class="cell small-6 medium-6 large-6 border-right">
							<div class="sections sections-left">
								<div class="list">
									<div class="list__card">
										<div class="list__img"><img width="140" height="90" src="<?php echo get_solo_image($cartao1); ?>" alt="<?php echo get_the_title($cartao1); ?>" title="<?php echo get_the_title($cartao1); ?>"></div>
										<div id="card1" class="change_card">
											<button type="button"><i class='bx bx-chevron-down'></i></button>
											<div class="list__title">
												<div class="item__headline">
													<h3 title="<?php echo get_the_title($cartao1); ?>"><?php echo get_the_title($cartao1); ?></h3>
												</div>
											</div>
										</div>
									</div>
									<div class="list__cta text-center">
										<a class="button expanded" target="_blank" data-offer-context="offer-card" href="<?php echo get_post_meta($cartao1,'stcode_cartao_link',true); ?>">Solicitar Cartão <i class="bx bx-check-shield icon-button"></i></a>
										<div class="color-gray"><small><i class="bx bx-check"></i> URL Verificada</small></div>
									</div>
			                    </div>
			                </div>
			            </div>
			            <div class="cell small-6 medium-6 large-6">
			            	<div class="sections sections-right">
			            		<div class="list">
									<div class="list__card">
										<div class="list__img"><img width="140" height="90" src="<?php echo get_solo_image($cartao2); ?>" alt="<?php echo get_the_title($cartao2); ?>" title="<?php echo get_the_title($cartao2); ?>"></div>
										<div id="card1" class="change_card">
											<button type="button"><i class='bx bx-chevron-down'></i></button>
											<div class="list__title">
												<div class="item__headline">
													<h3 title="<?php echo get_the_title($cartao2); ?>"><?php echo get_the_title($cartao2); ?></h3>
												</div>
											</div>
										</div>
									</div>
									<div class="list__cta text-center">
										<a class="button expanded" target="_blank" data-offer-context="offer-card" href="<?php echo get_post_meta($cartao1,'stcode_cartao_link',true); ?>">Solicitar Cartão <i class="bx bx-check-shield icon-button"></i></a>
										<div class="color-gray"><small><i class="bx bx-check"></i> URL Verificada</small></div>
									</div>
			                    </div>
			                </div>
			            </div>
			        </div>
			        <div class="row-section row-details">
						<div class="grid-x grid-padding-x">
							<div class="cell small-6 medium-6 large-6 border-right">
			                    <div class="list__details text-center">
			                    	<div class="wrap__card--row">
			                    		<i class="bx bx-calendar-event icon-review"></i>
			                    		<div><b>Anuidade</b> <?php echo get_post_meta($cartao1,'stcode_anuidade',true); ?></div>
			                    	</div>
			                    	<div class="wrap__card--row">
			                    		<i class="bx bxs-objects-vertical-bottom icon-review"></i>
			                    		<div><b>Limite de crédito</b><div> <?php echo get_post_meta($cartao1,'stcode_limite_credito',true); ?></div></div>
			                    	</div>
			                    	<div class="wrap__card--row">
			                    		<i class="bx bxl-meta icon-review"></i>
			                    		<div><b>Benefícios</b> <span> <?php echo get_terms_list_attribute( $cartao1, 'stcode_beneficios', 'name', 3); ?></span></div>
			                    	</div>
			                    	<div class="wrap__card--row">
			                    		<i class="bx bx-dollar-circle icon-review"></i>
			                    		<div><b>Renda mínima</b> <?php echo get_post_meta($cartao1,'stcode_renda_minima',true); ?></div>
			                    	</div>
			                    </div>
			            	</div>
			            	<div class="cell small-6 medium-6 large-6">
			            		<div class="list__details text-center">
			                    	<div class="wrap__card--row">
			                    		<i class="bx bx-calendar-event icon-review"></i>
			                    		<div><b>Anuidade</b> <?php echo get_post_meta($cartao2,'stcode_anuidade',true); ?></div>
			                    	</div>
			                    	<div class="wrap__card--row">
			                    		<i class="bx bxs-objects-vertical-bottom icon-review"></i>
			                    		<div><b>Limite de crédito</b><div> <?php echo get_post_meta($cartao2,'stcode_limite_credito',true); ?></div></div>
			                    	</div>
			                    	<div class="wrap__card--row">
			                    		<i class="bx bxl-meta icon-review"></i>
			                    		<div><b>Benefícios</b> <span> <?php echo get_terms_list_attribute( $cartao2, 'stcode_beneficios', 'name', 3); ?></span></div>
			                    	</div>
			                    	<div class="wrap__card--row">
			                    		<i class="bx bx-dollar-circle icon-review"></i>
			                    		<div><b>Renda mínima</b> <?php echo get_post_meta($cartao2,'stcode_renda_minima',true); ?></div>
			                    	</div>
			                    </div>
			                </div>
			            </div>
	                </div>
	                <div class="row-section row-anuidade first">
						<div class="grid-x grid-padding-x">
							<div class="cell small-12 medium-12 large-12"><span class="row-section__title">Anuidade</span></div>
							<div class="cell small-6 medium-6 large-6 border-right">
								<div class="sections sections-left">
									<div class="row-list">
										<div class="list__info"><?php echo get_post_meta($cartao1,'stcode_anuidade',true); ?></div>
									</div>
								</div>
							</div>
							<div class="cell small-6 medium-6 large-6">
								<div class="sections sections-right">
									<div class="row-list">
                                        <div class="list__info"><?php echo get_post_meta($cartao2,'stcode_anuidade',true); ?></div>
                                    </div>
                                </div>
							</div>
						</div>
					</div>
					<div class="row-section row-beneficios">
						<div class="grid-x grid-padding-x">
							<div class="cell small-12 medium-12 large-12"><span class="row-section__title">Benefícios do cartão</span></div>
							<div class="cell small-6 medium-6 large-6 border-right">
								<div class="sections sections-left">
									<div class="row-list">
										<div class="list__info">
											<?php 
											$beneficios = explode("\n",get_post_meta($cartao1,'stcode_beneficios_detalhados',true));
											if ($beneficios) {
												foreach ($beneficios as $beneficio) {
													echo '<p>'.html_entity_decode($beneficio).'</p>';
												}
											}
											?>
										</div>
									</div>
								</div>
							</div>
							<div class="cell small-6 medium-6 large-6">
								<div class="sections sections-right">
									<div class="row-list">
                                        <div class="list__info">
                                        	<?php 
											$beneficios = explode("\n",get_post_meta($cartao2,'stcode_beneficios_detalhados',true));
											if ($beneficios) {
												foreach ($beneficios as $beneficio) {
													echo '<p>'.html_entity_decode($beneficio).'</p>';
												}
											}
											?>
                                        </div>
                                    </div>
                                </div>
							</div>
						</div>
					</div>
					<div class="row-section row-bandeiras">
						<div class="grid-x grid-padding-x">
							<div class="cell small-12 medium-12 large-12"><span class="row-section__title">Benefícios da bandeira</span></div>
							<div class="cell small-6 medium-6 large-6 border-right">
								<div class="sections sections-left">
									<div class="row-list">
										<div class="list__info">
											<?php 
											$beneficios = explode("\n",get_post_meta($cartao1,'stcode_bandeira_detalhado',true));
											if ($beneficios) {
												foreach ($beneficios as $beneficio) {
													echo '<p>'.html_entity_decode($beneficio).'</p>';
												}
											}
											?>
										</div>
									</div>
								</div>
							</div>
							<div class="cell small-6 medium-6 large-6">
								<div class="sections sections-right">
									<div class="row-list">
                                        <div class="list__info">
                                        	<?php 
											$beneficios = explode("\n",get_post_meta($cartao2,'stcode_bandeira_detalhado',true));
											if ($beneficios) {
												foreach ($beneficios as $beneficio) {
													echo '<p>'.html_entity_decode($beneficio).'</p>';
												}
											}
											?>
                                        </div>
                                    </div>
                                </div>
							</div>
						</div>
					</div>
					<div class="row-section row-pros">
						<div class="grid-x grid-padding-x">
							<div class="cell small-12 medium-12 large-12"><span class="row-section__title">Prós</span></div>
							<div class="cell small-6 medium-6 large-6 border-right">
								<div class="sections sections-left">
									<div class="row-list">
										<div class="list__info">
											<?php
											$pros = explode(",",get_terms_list_attribute( $cartao1, 'pros', 'name', 0));
											if ($pros) {
												foreach ($pros as $pro) {
													echo "<div class=\"list__row\">
										                <div><i class=\"bx bx-check\"></i></div>
										                <div class=\"list__info\">$pro</div>
										            </div>";
										        }
										    }
										    ?>
										</div>
									</div>
								</div>
							</div>
							<div class="cell small-6 medium-6 large-6">
								<div class="sections sections-right">
									<div class="row-list">
                                        <div class="list__info">
                                        	<?php
											$pros = explode(",",get_terms_list_attribute( $cartao2, 'pros', 'name', 0));
											if ($pros) {
												foreach ($pros as $pro) {
													echo "<div class=\"list__row\">
										                <div><i class=\"bx bx-check\"></i></div>
										                <div class=\"list__info\">$pro</div>
										            </div>";
										        }
										    }
										    ?>
                                        </div>
                                    </div>
                                </div>
							</div>
						</div>
					</div>
					<div class="row-section row-contras">
						<div class="grid-x grid-padding-x">
							<div class="cell small-12 medium-12 large-12"><span class="row-section__title">Contras</span></div>
							<div class="cell small-6 medium-6 large-6 border-right">
								<div class="sections sections-left">
									<div class="row-list">
										<div class="list__info">
											<?php
											$contras = explode(",",get_terms_list_attribute( $cartao1, 'contras', 'name', 0));
											if ($contras) {
												foreach ($contras as $contra) {
													echo "<div class=\"list__row\">
										                <div><i class=\"bx bx-x\"></i></div>
										                <div class=\"list__info\">$contra</div>
										            </div>";
										        }
										    }
										    ?>
										</div>
									</div>
								</div>
							</div>
							<div class="cell small-6 medium-6 large-6">
								<div class="sections sections-right">
									<div class="row-list">
                                        <div class="list__info">
                                        	<?php
											$contras = explode(",",get_terms_list_attribute( $cartao2, 'contras', 'name', 0));
											if ($contras) {
												foreach ($contras as $contra) {
													echo "<div class=\"list__row\">
										                <div><i class=\"bx bx-x\"></i></div>
										                <div class=\"list__info\">$contra</div>
										            </div>";
										        }
										    }
										    ?>
                                        </div>
                                    </div>
                                </div>
							</div>
						</div>
					</div>
	            </div>
	        </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>