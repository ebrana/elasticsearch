# Elasticsearch

A simple mapping, indexing and filtering library on Elasticsearch...


- [Mapping](src/Elasticsearch/Mapping/README.md)
- [Indexing](src/Elasticsearch/Indexing/README.md)
- [Searching](src/Elasticsearch/Search/README.md)

### Satisfy
Balíček je sestaven pro platformu. Nejprve je nutné ručně přidat štítek do master větve.
Poté na adrese https://composer.ebrana.cz/admin pustit **"Build packages"**.
Následně je na platformě v composeru dostupná nová verze balíků.

### Docker
Nástroj lze kompletně provozovat v Dockeru. Pro jednoduchost lze použít připravené skripty přes make (Makefile).

Nejprve kontejner sestavíme pomocí
````
make build
````

Poté je možné si přidat do PHPStormu interpret **"elasticsearch-app"**.

Pro spuštění použij
````
make app
````

Kontejner podporuje xDebug. Kontejner je buildovan pro production mód.
Pro phpunit a phpstan proto po spuštění kontejneru je třeba ještě spustit
````
make composer-update
````
