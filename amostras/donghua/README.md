# Donghua

O cache de aplicação atende consultas de catálogo e outras páginas que podem depender de várias obras, grupos e filtros. A invalidação troca as versões que compõem a chave física, o que permite reconstruir as entradas afetadas sem percorrer todo o armazenamento em cada alteração editorial.

## Como o código se organiza

- [CacheManager.php](src/Cache/CacheManager.php) coordena leitura local e persistente, execução do callback em misses, espera por reconstrução concorrente, variação de TTL e métricas.
- [CacheVersionStore.php](src/Cache/CacheVersionStore.php) combina gerações global, de grupo e de tags. O armazenamento pode trocar uma geração por arquivo de forma atômica, com fallback nas opções do WordPress.
- [CacheLock.php](src/Cache/CacheLock.php) usa a operação de adição do object cache ou um lock por arquivo. O gerenciador espera por um intervalo curto quando outro processo está reconstruindo a chave.
- Os drivers de [arquivo](src/Cache/Drivers/FileCacheDriver.php) e [object cache](src/Cache/Drivers/ObjectCacheDriver.php) implementam a mesma [interface](src/Cache/CacheDriverInterface.php). [Configurações](src/Cache/CacheSettings.php) e [métricas](src/Cache/CacheMetrics.php) ficam em classes próprias.

## Verificação local

Na raiz do repositório, execute com PHP 8.2:

```sh
php amostras/donghua/verificar-cache.php
```

O script de portfólio simula as funções WordPress necessárias e usa um diretório temporário próprio. Ele verifica persistência, invalidação de tag e grupo, preservação de uma entrada de outra obra e exclusão mútua com dois objetos de lock. A integração com Redis, o painel e os gatilhos do tema dependem do ambiente original.
