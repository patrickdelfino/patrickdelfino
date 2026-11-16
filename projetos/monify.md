# Monify

Desenvolvi o Monify, um portal financeiro que reúne catálogos de cartões de crédito, empréstimos e contas digitais. Na versão 1.0.2, o visitante pode pesquisar produtos, comparar dois cartões e abrir a oferta. A estrutura do quiz também está presente, com recomendações personalizadas previstas para uma próxima etapa.

**Tecnologias principais** — WordPress, PHP, JavaScript, MySQL, CSS, AJAX, Composer e PSR-4.

## Arquitetura e catálogo

- **Organização do tema.** Usei Composer e PSR-4 no namespace STCode. As classes do tema implementam campos personalizados, painel de configurações, shortcodes, SEO e sitemap.
- **Cartões de crédito.** Modelei fichas com emissor, bandeira, anuidade, renda mínima, limite, benefícios, avaliação editorial, prós, contras e link para a oferta.
- **Empréstimos e contas digitais.** As fichas de empréstimo registram instituição, modalidade, valor, juros, prazo de liberação e parcelas. As fichas de contas digitais mostram tarifas, rendimento e produtos disponíveis. Ambas podem incluir avaliação editorial e link para a oferta.

## Busca e comparação

- **Catálogo filtrável.** A listagem de cartões tem busca, paginação e filtros por benefícios, categoria, bandeira e faixa de renda mínima exigida. O JavaScript atualiza a URL e recarrega os resultados via AJAX.
- **Comparador de cartões.** O visitante escolhe dois cartões para visualizar anuidade, limite, renda mínima, benefícios, prós e contras lado a lado. A comparação pode ser aberta por um link com os produtos selecionados.

## Conteúdo editorial e SEO

- **Blog e fichas de produto.** Implementei artigos vinculados aos produtos, arquivos de categorias e carregamento adicional via AJAX. Os shortcodes `[cartao]`, `[emprestimo]` e `[banco]` permitem inserir fichas em conteúdo editorial.
- **Home e painel.** A página inicial reúne abas de cartões, empréstimos e contas digitais, blog, FAQ e formulário de newsletter. O painel configura identidade visual, modo de manutenção, scripts, Analytics e Search Console.
- **URLs e sitemap.** O tema inclui rotas personalizadas para cartões, empréstimos e contas digitais, metadados de SEO, URLs canônicas, Open Graph, Twitter Cards e sitemap XML com índice por seção.

## Quiz e captação

- **Fluxo do quiz.** A estrutura existente contempla perguntas, navegação por etapas, captura de nome e e-mail, envio via AJAX e registros no painel. Na versão analisada, o cadastro de questionários está desativado. A tela final usa mensagem e destino predefinidos.
- **Newsletter.** O formulário está presente na home. O processamento das inscrições permanece pendente na versão documentada.

## Melhorias planejadas na versão atual

- **Painel e consultas administrativas · P0.** Reforçar permissões e validação das ações de salvamento. Definir critérios de busca e ordenação aceitos pelo servidor.
- **Requisições públicas · P0.** Limitar parâmetros e tamanho das consultas. Servir o sitemap gerado e controlar a regeneração por tarefa agendada ou ação administrativa.
- **Registros do quiz · P0.** Validar as respostas, limitar envios, retornar o resultado da gravação e definir acesso, retenção e exclusão dos contatos.
- **Comparador e renda · P1.** Tratar links incompletos, produtos removidos e seleções duplicadas. Distinguir a renda informada pelo visitante da renda mínima exigida por cada produto.
- **Home e newsletter · P1.** Tornar o FAQ e os dados de contato editáveis. Conectar o formulário a um fluxo de inscrição que mantenha o e-mail fora da URL.
- **Sitemap e URLs · P1.** Gerar XML em lotes, publicar arquivos de forma atômica e verificar status HTTP, canonicals e redirecionamentos de rotas legadas.

## Evoluções funcionais planejadas

- **Recomendação por perfil.** Usar as respostas do quiz para sugerir até três produtos, explicar cada indicação e oferecer envio opcional por e-mail.
- **Comparador ampliado.** Comparar até três cartões, filtrar diferenças e compartilhar a seleção. Aplicar o mesmo fluxo a contas digitais e empréstimos.
- **Simulador de empréstimos.** Estimar parcelas e total pago por valor, prazo e taxa, com cenários lado a lado e premissas visíveis.
- **Simulador de cashback e pontos.** Estimar o benefício anual a partir de gastos, anuidade, categorias elegíveis e tetos do cartão.
- **Gestão de ofertas.** Registrar fonte, vigência e situação, com fila de revisão, checagem de links, histórico e importação CSV com prévia e detecção de duplicatas.
- **Favoritos e alertas.** Salvar produtos no navegador e oferecer alertas por e-mail sobre disponibilidade, tarifas e anuidade.
- **Painel de conversão.** Acompanhar início e conclusão do quiz, uso do comparador e cliques nas ofertas. Integrar o retorno de conversão do parceiro quando houver API ou postback.
- **Painel de leads e CRM.** Organizar origem, campanha, resultado do quiz e situação do atendimento. Prever exportação restrita, deduplicação e envio ao CRM em fila com novas tentativas.
- **Consulta de CPF e score.** Avaliar uma integração futura em módulo separado, condicionada à escolha de um provedor de API, com chamadas pelo servidor, controle de acesso e auditoria.

A primeira entrega planejada prioriza as correções P0, o comparador e o quiz de recomendação de cartões. A gestão de ofertas e a medição de conversão vêm em seguida.
