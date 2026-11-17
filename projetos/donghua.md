# Donghua

Desenvolvi uma plataforma de streaming de donghua para reunir descoberta de obras, reprodução de episódios e interação da comunidade. O mesmo projeto inclui o painel que a equipe usa para administrar o catálogo, operar integrações, moderar usuários e acompanhar assinaturas.

**Tecnologias principais** — WordPress, PHP, JavaScript, MySQL, Composer, PSR-4, Codestar, AJAX, WP-Cron e WP-CLI.

## Arquitetura e catálogo

- **Organização do tema.** Separei catálogo, episódios, usuários, assinaturas, cache, sitemap e estatísticas em serviços. O tema usa autoload do Composer com PSR-4 e concentra as configurações administrativas no Codestar.
- **Modelo editorial.** Criei os tipos de conteúdo de obras e episódios, uma tabela de catálogo e taxonomias de gênero e estúdio. A ficha guarda títulos alternativos, sinopse, métricas, classificação, formato, ano, duração, imagens, trailer, relacionamentos entre obras e destaques editoriais.
- **Painel administrativo.** Implementei campos para importar e editar obras e episódios. O cadastro de episódios reúne número, temporada, metadados do TMDB e múltiplas fontes de reprodução.
- **Descoberta de conteúdo.** Usei o mesmo serviço de consulta para os catálogos de obras e filmes, com regras de formato, busca, filtros AJAX e paginação. O catálogo de filmes também oferece filtros por letra e gênero. A busca em tempo real no cabeçalho complementa uma página de resultados ordenados por relevância, com consultas a títulos, aliases, gêneros e estúdios. As rotas legadas de gênero redirecionam para os endereços atuais.
- **Páginas públicas.** A página da obra combina a ficha do catálogo com episódios em ordem numérica e seleção de temporadas via AJAX. A listagem carrega até 50 episódios por vez e mantém a temporada e a ordenação. Também desenvolvi a página de lançamentos, o template de notícias e uma barra lateral reutilizada para rankings, favoritos, discussões e conteúdo relacionado.
- **Página inicial configurável.** A equipe pode reordenar e ocultar seções no Codestar, incluindo destaques, lançamentos, filmes, tendências e favoritos. As seções pessoais carregam via AJAX. As recomendações consideram gêneros e estúdios do histórico e dos favoritos.

## Player e histórico

- **Reprodução dinâmica.** A troca de episódio ou fonte via AJAX atualiza a URL e carrega os comentários do episódio selecionado. A interface oferece navegação anterior e próximo, avanço automático e formulário para reportar problemas.
- **Múltiplos players.** Integrei JW Player, Plyr, Video.js, iframe e shortcode, com fallback para HLS e ajustes de interface, mobile e iOS. A atualização do JW Player 8.49.10 incluiu HLS e DASH, áudio alternativo, legendas, memória de preferências, Chromecast, AirPlay, Media Session e controles de TV. O Video.js também recebeu atualização para o pacote HTML da versão 10.
- **Continuar assistindo.** O histórico registra posição, duração e conclusão. O player retoma do ponto salvo e considera o episódio concluído a partir de 90%. O atalho de reprodução abre o episódio em andamento, avança para o seguinte após a conclusão ou inicia pelo primeiro episódio quando o usuário ainda não tem histórico.
- **Edição em massa.** O AJM Bulk Player Manager 1.0.0 permite localizar episódios por obra e adicionar, editar, remover ou substituir fontes de reprodução. O salvamento ocorre em lotes de 25 e reaproveita a estrutura de fontes do projeto.

## Contas, comunidade e notificações

- **Contas e autenticação.** Implementei cadastro, login, recuperação de senha, perfil público, avatar e páginas de favoritos, histórico, notificações, reports e assinatura. A autenticação inclui Google e Discord, hCaptcha validado no servidor, limitação de tentativas e restrições de acesso às rotas administrativas para usuários comuns.
- **Watch List.** Favoritar um episódio salva a obra associada. O estado do favorito é consultado via AJAX para funcionar com cache de página. O cabeçalho exibe prévias de favoritos e histórico.
- **Comentários e moderação.** Desenvolvi respostas aninhadas, ordenação, votos, menções, spoilers e denúncias. O painel permite administrar usuários, comentários e reports. A atualização 10.1.4 ajustou o botão de resposta, a ordenação das abas e a exibição imediata de novos comentários.
- **Reputação e notificações internas.** A comunidade tem níveis, conquistas, distintivos, identificação da equipe e rankings. A central de notificações oferece contador, preferências e marcação de leitura para respostas, menções, curtidas, conquistas, novos episódios de favoritos e atualizações de reports.
- **Telegram Notifier 1.1.0.** O plugin publica em canais e grupos, vincula contas pelo bot e usa webhook e fila assíncrona com novas tentativas. O usuário pode receber episódios de todo o catálogo ou apenas de sua Watch List.

## Assinaturas e publicidade

- **Ciclo de assinatura.** Integrei planos, faturas, renovação, troca de plano, cancelamento e reativação à área da conta. O módulo inclui Pix manual, Mercado Pago e Asaas Pix Automático, com QR Code, Pix copia e cola e webhooks.
- **Conferência de Pix manual.** O usuário envia os dados da transação ou um comprovante. A fatura fica em análise até a conferência administrativa. A aprovação registra o pagamento e ativa ou renova a assinatura.
- **Benefícios e anúncios.** Os planos podem controlar anúncios, acesso a downloads e conteúdo exclusivo. O painel de publicidade permite configurar posições, dispositivos e carregamento sob demanda, com inserção manual por slug.

## Importação e migração

- **AJM Donghua Importer.** Integrei MyAnimeList e TMDB com busca por título ou ID, prévia de correspondência, seleção de campos, importação de temporadas e atualização de episódios. As opções ficam no Codestar.
- **Deduplicação e correspondência.** O importador procura primeiro pelos IDs externos e depois por títulos e aliases normalizados. Correspondências ambíguas passam por revisão manual. A classificação considera obra, filme, especial e temporada isolada. As atualizações preservam títulos editoriais e corrigem rótulos de episódio duplicados.
- **Atualização em lote.** Implementei fila com progresso, histórico, pausa e retomada, executada por WP-Cron ou WP-CLI. Locks evitam workers simultâneos. A atualização permite escolher campos ou preencher apenas valores vazios. A v1.8.1 corrigiu a configuração dos workers, a v1.9.0 ampliou a detecção por tipo e temporada e a v1.10.0 adicionou a fila de revisão de candidatos abaixo do limiar de confiança.
- **Mídia.** A importação baixa posters, backdrops, galerias e imagens de episódios. As versões 1.10.1 a 1.10.4 aprimoraram a atualização de imagens, o diagnóstico de downloads, a recuperação de anexos sem arquivo e os nomes derivados do título da obra.
- **Migrator & Validator e Donghua Migrator.** Implementei a migração do catálogo legado com preservação de IDs, taxonomias e mídia, além do remapeamento dos vínculos entre obras e episódios. O fluxo usa backup, validação, recuperação de vínculos, checkpoints e processamento em segundo plano.

## SEO, desempenho e operação

- **URLs e sitemap.** Centralizei a configuração de permalinks, com URLs por publicação, histórico de aliases e redirecionamento 301 para o endereço final. A busca integra o canonical do Rank Math. O sitemap funciona com Rank Math ou com o WordPress nativo, inclui controles por tipo de conteúdo, taxonomia e imagem, respeita os permalinks personalizados e exclui rotas privadas.
- **Cache de aplicação.** Usei object cache persistente quando disponível, com arquivos como fallback. Catálogo, busca, barra lateral, página inicial e rankings usam cache com invalidação por versão ou dependência. A limpeza ocorre em lotes. Locks por chave evitam reconstruções simultâneas e a variação de TTL distribui as expirações.
- **Banco de dados.** Versionei o esquema e adicionei índices para consultas recorrentes, contadores pré-calculados para contas e comentários e agregados para a página inicial e os rankings. Os registros antigos são preenchidos de forma incremental.
- **Rotina de operação.** O painel inclui saúde do sistema, métricas de cache, status do cron, limpeza de registros órfãos e modo de manutenção. Também implementei conversão de imagens enviadas ou importadas para WebP, ajustes responsivos e configurações para Analytics, Tag Manager, Search Console, Facebook Pixel, lazy loading, identidade visual e scripts personalizados.

## Código

[Arquivos do Donghua](../amostras/donghua/README.md)

O código publicado reúne os serviços de cache do projeto. A leitura começa pelo CacheManager e segue pelas classes de versões, tags, locks e pelos drivers de arquivo e object cache. O README da pasta explica as dependências e inclui um teste opcional em PHP para acompanhar a invalidação.

