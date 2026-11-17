# MaxStreaming

Criei o MaxStreaming, um CMS próprio para administrar e apresentar um catálogo audiovisual. Desenvolvi tanto o painel usado para cadastrar o acervo quanto a interface pública, inspirada na navegação por categorias e destaques da Netflix e do Disney+.

**Tecnologias principais** — PHP, MySQL, PDO, FastRoute, Composer, JavaScript, APIs JSON, TMDB e HLS.

## Arquitetura e catálogo

- **Organização da aplicação.** Usei FastRoute para separar rotas públicas, administrativas e de API. Os controladores encaminham as ações para templates próprios de catálogo, painel e mobile.
- **Modelo de dados.** Estruturei tabelas MySQL para filmes, séries, animes, temporadas, episódios e canais ao vivo. O modelo também registra gêneros, elencos e fontes de vídeo. A camada de acesso usa PDO.
- **Relações do catálogo.** Os gêneros e o elenco podem ser vinculados aos títulos. Temporadas e episódios têm registros próprios para organizar o acervo e suas fontes de reprodução.

## Painel editorial e integração com TMDB

- **Gestão de conteúdo.** O painel reúne telas para editar títulos, temporadas, episódios, gêneros e elencos. Também oferece gestão de usuários, login, uploads e ações em lote para manutenção do catálogo.
- **Metadados do TMDB.** Integrei consultas de metadados ao fluxo de cadastro. Conforme o tipo de título, o editor pode preencher informações como imagens, gêneros e trailer antes de gravar no catálogo local.
- **Home configurável.** Implementei controles para ativar, desativar e reordenar módulos da página inicial. O painel também permite selecionar títulos em destaque e organizar vitrines de filmes, séries e animes.

## Navegação e reprodução

- **Interface pública.** Criei páginas para desktop e mobile, busca unificada por filmes, séries e animes, prévias de títulos e navegação por gênero. As páginas de gênero carregam resultados adicionais sob demanda.
- **APIs do catálogo.** Os endpoints retornam prévias de títulos, resultados de busca, listagens por gênero e episódios vinculados a temporadas. A busca entrega dados estruturados para mobile e trechos de interface para desktop.
- **Playlist HLS.** A API consulta as fontes cadastradas para o título e monta uma playlist JSON para o player, com identificação das opções de reprodução.

## Desempenho, SEO e operação

- **Cache de páginas.** Desenvolvi uma classe com armazenamento, expiração, limpeza e pré-carregamento. O acionamento dessa classe pelo fluxo público ficou sem confirmação na versão analisada.
- **Sitemap.** Implementei uma classe para gerar índice e páginas de sitemap por tipo de conteúdo. A ligação do gerador às rotas ativas ficou sem confirmação nessa cópia.
- **Instalação e migração.** Incluí rotinas para criar o esquema do banco e transferir dados de uma estrutura anterior para as tabelas do CMS.

## Estado da versão analisada

A API de episódios retorna até 12 itens e calcula os indicadores da próxima página. A aplicação do deslocamento na consulta ainda precisa ser ajustada para avançar pelos resultados. A revisão acima se baseia no código do projeto.

## Amostra técnica

[Ver recortes do código original](../amostras/maxstreaming/README.md) com configuração e renderização da home editorial, destaques e geração de sitemap.
