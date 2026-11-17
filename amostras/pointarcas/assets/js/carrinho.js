jQuery(function($) {
    // Seletor do botão Adicionar ao Carrinho
    var addToCartButton = document.querySelector('.botao-carinho');

    // Verifica se o botão foi encontrado
    if (addToCartButton) {
        addToCartButton.addEventListener('click', function() {
            // Aqui você deve definir as informações do produto a serem adicionadas ao carrinho
            var productId = $(this).data('productid'); // Substitua pelo ID do seu produto
            var quantity = 1; // Substitua pela quantidade desejada

            console.log(productId);

            // Adiciona o produto ao carrinho via AJAX
            jQuery.ajax({
                type: 'POST',
                url: wc_add_to_cart_params.ajax_url,
                data: {
                    action: 'add_to_cart',
                    product_id: productId,
                    quantity: quantity
                },
                success: function(response) {
                    // Exibe o número de itens no elemento desejado
                    $('.cart-count').text(response);
                }
            });
        });
    }

    $.ajax({
        type: 'GET',
        url: wc_add_to_cart_params.ajax_url,
        data: {
            action: 'get_cart_count'
        },
        success: function(response) {
            // Exibe o número de itens no elemento desejado
            $('.cart-count').text(response);
        }
    });

    // Função para marcar/desmarcar todos os checkboxes
    const checkboxes = document.querySelectorAll('.stardust-checkbox__input');
    const selectAllCheckbox = document.querySelector('.stardust-checkbox__input[aria-label="clique aqui para selecionar todos os produtos"]');
    let subtotalElement = document.querySelector('.count_subtotal_price bdi');
    let totalElement = document.querySelector('.count_total_price bdi');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                checkbox.closest('.stardust-checkbox').classList.toggle('stardust-checkbox--checked', this.checked);
            });
            updatePrices();
        });

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updatePrices();
                this.closest('.stardust-checkbox').classList.toggle('stardust-checkbox--checked', this.checked);
            });
        });
    }

    // Link para finalizar a compra via WhatsApp
    const finishButton = document.getElementById('finesh_product');
    if (finishButton) {
        finishButton.addEventListener('click', function() {
            let selectedProducts = '';
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const row = checkbox.closest('tr');
                    
                    // Adicione verificações para os elementos antes de acessar as propriedades
                    const productNameElement = row.querySelector('.JB57cn');
                    const quantityElement = row.querySelector('.qty span');
                    const priceElement = row.querySelector('.woocommerce-Price-amount');

                    if (productNameElement && quantityElement && priceElement) {
                        const productName = productNameElement.textContent;
                        const quantity = parseInt(quantityElement.textContent);
                        const price = parseFloat(priceElement.textContent.replace('R$', '').trim());
                        selectedProducts += `${productName}: ${quantity} unidades - Total: R$ ${price * quantity}\n`;
                    }
                }
            });

            const totalPrice = parseFloat(subtotalElement.textContent.replace('R$', '').trim());
            const whatsappLink = `https://web.whatsapp.com/send?phone=`+STCode.number+`&text=Olá, estou interessado nos seguintes produtos:%0A${selectedProducts}Total: R$ ${totalPrice.toFixed(2)}%0AGostaria de finalizar a compra.`;

            window.open(whatsappLink);
        });
    }
});

// Função para atualizar os preços
function updatePrices() {
    const checkboxes = document.querySelectorAll('.stardust-checkbox__input');
    let subtotalElement = document.querySelector('.count_subtotal_price bdi');
    let totalElement = document.querySelector('.count_total_price bdi');

    let totalPrice = 0;

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const row = checkbox.closest('tr');
            const priceElement = row.querySelector('.woocommerce-Price-amount');
            if (priceElement) {
                const priceText = priceElement.textContent.trim().replace(/[^\d,]/g, ''); // Remover caracteres não numéricos, exceto vírgula
                const price = parseFloat(priceText.replace(',', '.')); // Substituir vírgula por ponto para garantir que o parseFloat funcione corretamente

                if (!isNaN(price)) {
                    const quantity = parseInt(row.querySelector('.qty span').textContent);
                    totalPrice += price * quantity;
                } else {
                    console.error('Erro ao ler o preço do produto.');
                }
            }
        }
    });

    if (!subtotalElement || !totalElement) {
        subtotalElement = document.querySelector('.count_subtotal_price bdi');
        totalElement = document.querySelector('.count_total_price bdi');
    }

    // Formatar o subtotal e total corretamente
    let formattedTotalPrice = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(totalPrice);

    if (subtotalElement && totalElement) {
        subtotalElement.textContent = formattedTotalPrice;
        totalElement.textContent = formattedTotalPrice;
    }
}

// Função para diminuir a quantidade
function decreaseQuantity(element) {
    let quantityElement = element.nextElementSibling;
    let quantity = parseInt(quantityElement.textContent);
    if (quantity > 1) {
        quantity--;
        quantityElement.textContent = quantity;
        updateTotalPrice(element);
        updatePrices();
    }
}

// Função para aumentar a quantidade
function increaseQuantity(element) {
    let quantityElement = element.previousElementSibling;
    let quantity = parseInt(quantityElement.textContent);
    quantity++;
    quantityElement.textContent = quantity;
    updateTotalPrice(element);
    updatePrices();
}

// Função para atualizar o preço total baseado na quantidade
function updateTotalPrice(element) {
    let row = element.closest('tr');
    let priceElement = row.querySelector('.singular_price_single');
    let priceText = priceElement.textContent.trim().replace(/[^\d,]/g, ''); // Remover caracteres não numéricos, exceto vírgula
    let price = parseFloat(priceText.replace(',', '.')); // Substituir vírgula por ponto para garantir que o parseFloat funcione corretamente

    let quantityElement = row.querySelector('.qty span');
    let quantity = parseInt(quantityElement.textContent);
    let totalPriceElement = row.querySelector('.total_price_single');
    let totalPrice = price * quantity;

    // Formatar o preço total corretamente
    let formattedTotalPrice = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(totalPrice);

    totalPriceElement.textContent = formattedTotalPrice;
}

document.addEventListener('DOMContentLoaded', function() {
    const removeButtons = document.querySelectorAll('.remove-item');
    
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const itemId = row.dataset.itemId; // Obtém o ID do item do carrinho

            removeProductFromCart(itemId, row);
        });
    });
});

function removeProductFromCart(itemId, row) {
    // Adiciona o produto ao carrinho via AJAX
    jQuery.ajax({
        type: 'POST',
        url: wc_add_to_cart_params.ajax_url,
        data: {
            action: 'remove_to_cart',
            remove_item: itemId
        },
        success: function(response) {
            // Remove o elemento <tr> que representa o item removido
            row.remove();
        }
    });
}