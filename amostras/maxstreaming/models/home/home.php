<?php
// Modules
$default = array(
    'populares_movies',
    'populares_series',
    'animes',
    'series',
    'filmes',
    'episodios',
    'top-imdb'
);

$modulo = get_modulos_home();
$modules = $modulo ? $modulo : $default;

if(!empty($modules)){
    // Get template
    foreach($modules as $template => $template_name) {
        include_once('models/home/parts/'.$template_name.'.php');
    }
}
?>