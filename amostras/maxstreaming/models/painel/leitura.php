<?php
$modulos_salvos = get_modulos_home();

// Lista de todos os módulos possíveis
$modulos_disponiveis = [
    'populares_movies',
    'populares_series',
    'animes',
    'series',
    'filmes',
    'episodios',
    'top-imdb'
];

// Separar os ativos dos inativos
$modulos_ativos = $modulos_salvos;
$modulos_inativos = array_values(array_diff($modulos_disponiveis, $modulos_ativos));
?>
<form method="post">
    <input type="hidden" name="nonce" value="<?= update_nonce('save_modulos_action'); ?>">
    <div class="block justify-between page-header md:flex">
        <div>
            <h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 dark:text-white dark:hover:text-white text-[1.125rem] font-semibold">Modulos</h3>
        </div>
        <button type="submit" class="ti-btn bg-primary m-1 !font-medium">Salvar Alterações</button>
    </div>
    <div class="grid grid-cols-12 gap-6">
        <div class="xl:col-span-6 col-span-12" id="draggable-left">
            <div class="csf-sorter-wrapper">
                <div class="csf-modules">
                    <h3 class="text-[1.25rem]">Módulos Ativados</h3>
                    <ul id="modulos-ativos" class="sortable-modulos dragula-area connected-sortable">
                        <?php
                        if ($modulos_ativos) {
                            foreach ($modulos_ativos as $modulo) {
                                render_modulo($modulo);
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="xl:col-span-6 col-span-12" id="draggable-right">
            <div class="csf-sorter-wrapper">
                <div class="csf-modules">
                    <h3 class="text-[1.25rem]">Módulos Desativados</h3>
                    <ul id="modulos-inativos" class="sortable-modulos dragula-area connected-sortable">
                        <?php
                        if ($modulos_inativos) {
                            foreach ($modulos_inativos as $modulo) {
                                render_modulo($modulo, false);
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>