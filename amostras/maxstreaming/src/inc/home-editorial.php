<?php

// Recortes de src/inc/functions.php. O objeto de banco é fornecido pelo CMS.

function salvar_modulos_home(array $modules) {
    global $db;
    
    $json = json_encode($modules);

    // Verifica se já existe configuração
    $db->where('chave', 'homepage_modules');
    $exists = $db->getOne('configuracoes');

    if ($exists) {
        $db->where('chave', 'homepage_modules')->update('configuracoes', ['valor' => $json]);
    } else {
        $db->insert('configuracoes', ['chave' => 'homepage_modules', 'valor' => $json]);
    }
}

function get_modulos_home(): array {
    global $db;

    $db->where('chave', 'homepage_modules');
    $config = $db->getOne('configuracoes');

    if ($config && !empty($config['valor'])) {
        return json_decode($config['valor'], true);
    }

    return []; // vazio se não tiver configuração ainda
}

function getFeaturedHighlights($currentPage = '') {
    global $db;

    // Pega todos os destaques ativos
    $db->where('active', 1);
    $db->orderBy('ordem', 'asc');
    $highlights = $db->get('featured_highlights');

    $result = [];

    if ($highlights) {
        foreach ($highlights as $highlight) {
            $pages = $highlight['pages'];

            if (empty($currentPage) || $currentPage === $pages) {
                $result[] = $highlight;
            }
        }
    }

    return $result;
}
