# PointArcas

O recorte acompanha dois fluxos da loja WooCommerce. O cliente filtra a vitrine e pode encaminhar ao atendimento os itens que selecionou no carrinho.

## Da URL à vitrine

- [Filtros em JavaScript](assets/js/filtro.js) atualiza parâmetros de taxonomia e preço na URL, busca o HTML e troca o trecho da listagem.
- [Consulta do catálogo](woocommerce/archive-product.php) combina taxonomias, preço e paginação em WP_Query. A consulta renderiza os cards e a navegação usada pelo JavaScript.

## Do carrinho ao atendimento

- [Template do carrinho](woocommerce/cart/cart.php) apresenta seleção de itens, controles de quantidade e subtotais no carrinho personalizado.
- [JavaScript do carrinho](assets/js/carrinho.js) consulta e atualiza o carrinho via AJAX, calcula o resumo dos itens selecionados e prepara a mensagem de WhatsApp. O número de atendimento é injetado pela configuração do tema.

Os templates dependem do WordPress e do WooCommerce. No carrinho publicado neste recorte, o frete aparece como **A Combinar** e a continuidade da compra ocorre pelo atendimento.
