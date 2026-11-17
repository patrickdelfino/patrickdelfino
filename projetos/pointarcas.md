# PointArcas

Desenvolvi do zero a PointArcas, uma loja virtual sobre WordPress e WooCommerce. O projeto inclui a vitrine, as páginas de produto e um carrinho personalizado que encaminha ao WhatsApp um resumo dos itens selecionados para a equipe dar continuidade ao atendimento.

**Tecnologias principais** — WordPress, WooCommerce, PHP, JavaScript, AJAX e ACF.

## Arquitetura e catálogo

- **Organização do tema.** Desenvolvi templates para a vitrine, as listagens, as fichas de produto e o carrinho, usando os componentes do WooCommerce para catálogo, sessão de compra e opções de entrega.
- **Modelagem dos produtos.** Registrei taxonomias próprias para tipo de produto, marca, capacidade, munição e calibre. O painel também permite adicionar um resumo e vincular conteúdo relacionado a cada ficha.
- **Filtros e busca.** A listagem combina taxonomias e faixa de preço com paginação. O JavaScript atualiza os filtros na URL e recarrega os resultados sem recarregar a página inteira. A busca da loja é direcionada aos produtos.
- **Vitrines e banners.** A página inicial inclui banners e carrosséis. A equipe pode configurar categorias, quantidade e ordem dos itens em destaque. Os slides têm cadastro próprio com campos configurados via ACF.

## Ficha de produto e avaliações

- **Apresentação do produto.** A ficha inclui galeria com zoom, imagens responsivas, indicação de estoque, preço promocional, descrição e produtos relacionados.
- **Avaliações.** Implementei um formulário com estrelas e comentários, envio via AJAX e carregamento adicional de avaliações na página do produto.
- **Mídia e dados estruturados.** O tema inclui suporte a imagens WebP e oferece opção de exibir dados estruturados de produto, como SKU, preço, disponibilidade e avaliações.

## Carrinho e atendimento via WhatsApp

- **Operações do carrinho.** Implementei adição, remoção e contagem de itens via AJAX, além da seleção individual ou em grupo dos produtos.
- **Resumo da compra.** A interface permite ajustar quantidades e recalcular o subtotal exibido para os itens selecionados. O botão de finalização monta uma mensagem de WhatsApp com os nomes dos produtos, as quantidades e o total apresentado no carrinho.
- **Atendimento pela ficha.** A página de produto também oferece um atalho para o WhatsApp com os dados do item, permitindo iniciar o atendimento diretamente da vitrine.

## Frete e encaminhamento ao checkout

- **Templates do WooCommerce.** O tema inclui calculadora de frete com campos de endereço, seleção de método de entrega e botão para acessar o checkout padrão.
- **Fluxo da versão analisada.** No carrinho personalizado, o frete aparece como **A Combinar** e o botão principal encaminha a seleção ao WhatsApp. A ativação de um gateway de pagamento ficou sem confirmação no código do tema analisado.

## Código

[Arquivos do PointArcas](../amostras/pointarcas/README.md)

Os arquivos acompanham o caminho dos filtros até a consulta do WooCommerce e, depois, o carrinho personalizado e a montagem da mensagem para o WhatsApp. Os templates usam o WordPress, o WooCommerce e as configurações do tema como base.

