# Recortes do código original

O portfólio reúne módulos que desenvolvi em STCODE e AJM durante minha atuação na Sarthemes e na Ajaimotech. Os recortes preservam os namespaces, comentários e atribuições dos arquivos de origem. Onde o código foi extraído de um arquivo maior, a documentação indica o limite do trecho.

| Projeto | Recorte | O que observar |
| --- | --- | --- |
| [Donghua](donghua/README.md) | Cache de aplicação | Chaves por geração e tag, controle de concorrência, drivers de arquivo e object cache, variação de TTL e métricas |
| [MaxStreaming](maxstreaming/README.md) | Home editorial e sitemap | A ordem definida no painel passa pela persistência até chegar aos templates públicos |
| [Monify](monify/README.md) | Modelagem de cartões e comparador | Tipos de conteúdo, campos e taxonomias formam a apresentação lado a lado |
| [PointArcas](pointarcas/README.md) | Catálogo filtrável e carrinho | Estado dos filtros na URL, consulta WooCommerce e preparação de um resumo para atendimento via WhatsApp |

As dependências e os limites de execução estão indicados na página de cada recorte. O Donghua inclui uma verificação de CLI para executar o driver de arquivos e a lógica de invalidação com um ambiente WordPress simulado.
