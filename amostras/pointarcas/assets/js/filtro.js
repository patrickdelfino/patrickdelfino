// Função para carregar conteúdo na div
function carregarConteudoNaDiv(url) {
    // Exibe o overlay de carregamento
    document.querySelector('.overlay').style.display = 'flex';

    fetch(url)
        .then(response => response.text())
        .then(data => {
            // Encontra o conteúdo da classe .tax-products-itens na resposta da requisição
            const fragment = document.createRange().createContextualFragment(data);
            const taxProductsContent = fragment.querySelector('.tax-products-itens').innerHTML;

            // Atualiza o conteúdo da div com o HTML obtido
            document.querySelector('.tax-products-itens').innerHTML = taxProductsContent;

            // Esconde o overlay após carregar o conteúdo
            document.querySelector('.overlay').style.display = 'none';
        })
        .catch(error => {
            console.error('Houve um erro ao carregar o conteúdo:', error);
            // Em caso de erro, também esconda o overlay
            document.querySelector('.overlay').style.display = 'none';
        });
}

// Função para adicionar ou atualizar parâmetros na URL
function atualizarURL(parametro, valor) {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);

    // Se o input for do tipo radio, substitui o valor existente
    if (document.querySelector(`input[data-keynormalizedname="${parametro}"][type="radio"]`)) {
        params.delete(parametro);
    }

    const valoresExistentes = params.get(parametro);

    // Restante do código para atualizar os parâmetros na URL...
    // Adiciona os novos parâmetros
    if (valoresExistentes) {
        params.set(parametro, `${valoresExistentes},${valor}`);
    } else {
        params.set(parametro, valor);
    }

    // Atualiza a URL na barra de endereço sem recarregar a página
    history.replaceState({}, '', `${window.location.pathname}?${params}`);

    // Após atualizar a URL, chame a função para carregar o conteúdo na div
    carregarConteudoNaDiv(window.location.href);
}

// Adiciona um listener para os checkboxes e radios
const inputs = document.querySelectorAll('.filter__rule-input');
inputs.forEach(input => {
    input.addEventListener('change', function() {
        const filtro = this.value;
        const parametro = this.getAttribute('data-keynormalizedname');

        atualizarURL(parametro, filtro);
    });
});

// Função para adicionar funcionalidade aos sliders e inputs de faixa de preço
function setupFaixaPreco() {
    const fromSlider = document.querySelector('.fromSlider');
    const toSlider = document.querySelector('.toSlider');
    const fromInput = document.querySelector('.fromInput');
    const toInput = document.querySelector('.toInput');

    // Função para atualizar a URL com os valores de faixa de preço
    function atualizarFaixaPreco() {
        const minPrice = fromInput.value;
        const maxPrice = toInput.value;

        // Certifica-se de que min_price não seja maior que max_price
        if (parseFloat(minPrice) > parseFloat(maxPrice)) {
            // Troca os valores se necessário
            fromInput.value = maxPrice;
            toInput.value = minPrice;
        }

        // Obtém a URL atual
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);

        // Remove os parâmetros existentes
        params.delete('min_price');
        params.delete('max_price');

        // Adiciona os novos parâmetros
        params.append('min_price', fromInput.value);
        params.append('max_price', toInput.value);
    }

    function atualizarURL() {
        const minPrice = fromInput.value;
        const maxPrice = toInput.value;

        // Certifica-se de que min_price não seja maior que max_price
        if (parseFloat(minPrice) > parseFloat(maxPrice)) {
            // Troca os valores se necessário
            fromInput.value = maxPrice;
            toInput.value = minPrice;
        }

        // Obtém a URL atual
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);

        // Remove os parâmetros existentes
        params.delete('min_price');
        params.delete('max_price');

        // Adiciona os novos parâmetros
        params.append('min_price', fromInput.value);
        params.append('max_price', toInput.value);

        // Atualiza a URL na barra de endereço sem recarregar a página
        history.replaceState({}, '', `${window.location.pathname}?${params}`);

        // Após atualizar a URL, chame a função para carregar o conteúdo na div
        carregarConteudoNaDiv(window.location.href);
    }

    // Atualiza os valores quando os sliders são movidos
    fromSlider.addEventListener('mouseup', function() {
        fromInput.value = this.value;
        atualizarURL();
    });

    toSlider.addEventListener('mouseup', function() {
        toInput.value = this.value;
        atualizarURL();
    });

    // Atualiza os valores quando os inputs são alterados manualmente
    fromInput.addEventListener('change', atualizarURL);
    toInput.addEventListener('change', atualizarURL);
}

// Chama a função para configurar a faixa de preço
setupFaixaPreco();
