# Monify

O recorte mostra como o tema WordPress modela os produtos financeiros e usa os dados cadastrados para comparar dois cartões.

## Do cadastro para a comparação

- [Registro de cartões](inc/library/cartao-de-credito.php) define o tipo de conteúdo e os campos administrativos, como anuidade, limite, renda mínima e link da oferta. O recorte inclui o registro e a apresentação dos campos.
- [Taxonomias](inc/library/taxonomys.php) organiza prós, contras, perfis, bandeiras, benefícios e categorias. Parte das taxonomias é compartilhada entre cartões, empréstimos e contas digitais.
- [Template do comparador](inc/parts/comparar.php) resolve a seleção e organiza as duas fichas lado a lado com metadados e termos do WordPress. O template pertence à versão 1.0.2 e pressupõe que a seleção já contém dois cartões existentes. O tratamento de links incompletos e produtos removidos consta no planejamento do estudo de caso.

Os recortes dependem do WordPress e dos helpers do tema. O salvamento administrativo e os registros de contato do quiz estão fora deste recorte.
