# Elasticsearch

A simple mapping, indexing and filtering library on Elasticsearch...


- [Mapping](src/Elasticsearch/Mapping/README.md)
- [Indexing](src/Elasticsearch/Indexing/README.md)
- [Searching](src/Elasticsearch/Search/README.md)

### Docker
Pro jednoduchost lze použít připravené skripty přes make (Makefile).

Nejprve kontejner sestavíme pomocí:
````
make build
````
Poté je možné si přidat do PHPStormu interpret **"elasticsearch-app"**.

Spuštění kontejneru:
````
make up
````

Kontejner podporuje xDebug a je buildován pro production mód.
Pro používání phpunit a phpstan je třeba ještě spustit (kontejner musí být spuštěn)
````
make composer-update
````

### Symfony bundle
https://github.com/ebrana/elasticsearch-bundle
