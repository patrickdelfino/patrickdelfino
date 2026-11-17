# MaxStreaming

O recorte mostra como a equipe editorial controla a página inicial do CMS em PHP e MySQL.

## Do painel para a página inicial

- [Configuração editorial](models/painel/leitura.php) apresenta os módulos ativos e inativos em listas ordenáveis e gera a configuração usada pelo CMS.
- [Persistência e destaques](src/inc/home-editorial.php) reúne três funções extraídas de `src/inc/functions.php`. A ordem é salva como JSON na configuração do CMS. Os destaques são consultados pela ordem editorial e filtrados pela página em que devem aparecer.
- [Template público](models/home/home.php) resolve a ordem salva e percorre os templates dos módulos. O arquivo inclui uma lista padrão para quando a configuração está vazia.

## Sitemap

A [classe de sitemap](src/class/Sitemap.php) contém a geração do índice e das páginas por tipo de conteúdo, com saídas XML e HTML. A integração do gerador às rotas públicas ficou sem confirmação na cópia analisada. Os templates do CMS dependem do bootstrap e do banco da aplicação.
