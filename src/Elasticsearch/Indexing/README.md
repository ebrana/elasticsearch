# Elasticsearch - Indexing

Pro indexování dat je možné využít vytváření "dokumentů" přes **DocumentFactory**.
DokumetFactory potřebuje zaregistrovat konkrétní dokument builder (respektive jeho factory), který je potřeba vytvořit
vždy pro konkrétní entitu (kvůli typovosti se předává **IndexableEntityInterface**). Dokument builder vychází z 
**DocumentBuilderInterface** a ten přes build metodu vrací objekt **DocumentInterface**. Ideálně však využít 
předpřipravený objekt **Document**.

Pokud využíváme mapování relací přes context a mappedBy, tak můžeme využívat **DefaultDocumentBuilderFactory**.
Více v ukázce kódu []() > [Ukázka použítí](../../../examples/indexData.php) <

Pro resolvování dat uvnitř builderu můžeme použít rozšíření:

- ScalarValueResolverTrait
- CollectionValueResolverTrait
- CollectionByMappingResolverTrait

### ScalarValueResolverTrait
Pro resolvování skalárních hodnot. Její rozhranní je

`````
private function resolveScalarByMetadata(
      DocumentInterface $document,
      IndexableEntityInterface $entity,
      Index $index
): void
`````

### CollectionByKeyResolverTrait
Pro rezolvování dat v kolekcích. Například pokud mám překladovou tabulku (*_langs).
Její rozhranní je

`````
private function resolveCollectionsByField(
      DocumentInterface $document,
      IndexableEntityInterface $entity,
      AbstractType $field,
      callable $keyResolver = null,
      ?callable $valueResolver = null
): void
`````
Oproti skalárním hodnotám předávám konkrétní field (ten získám z mappingu $index->getProperties()->get('parameters')).
Protože kolekce musí vracet objekt, tak mohu využít 2 callback resolvery pro klíč a hodnotu.
Například:
`````
$this->resolveCollectionsByField(
      $document,
      $entity,
      $this->index->getProperties()->get('sellingPriceWithVat'),
      function (ProductModuleLangs $langs) {
            return '@' . $langs->getLang();
      }
);
`````

## Bulk API

Pro dávkovou indexaci je `Connection::bulk()`. Do jedné dávky lze míchat operace i indexy.

```php
use Elasticsearch\Connection\Params\BulkParams;
use Elasticsearch\Indexing\Bulk\BulkRequest;

$request = new BulkRequest();
$request->index($document)                              // zaindexuje, případně přepíše
    ->create($jinyDocument)                             // jen když _id ještě neexistuje
    ->update($index, '3', ['price' => 150], docAsUpsert: true)
    ->delete($index, '4');

$response = $connection->bulk($request, new BulkParams(refresh: true));
```

Pro operace, které potřebují víc voleb, jsou třídy `IndexOperation`, `CreateOperation`,
`UpdateOperation` a `DeleteOperation` a metoda `add()`:

```php
use Elasticsearch\Indexing\Bulk\UpdateOperation;

$request->add(new UpdateOperation($index, '2',
    script: ['source' => 'ctx._source.views += 5'],
    retryOnConflict: 3
));
```

### ⚠️ Kontrola chyb je povinná

**Bulk vrací HTTP 200 i když část položek selže** — bez kontroly by chyby zapadly:

```php
if ($response->hasErrors()) {
    foreach ($response->getErrors() as $error) {
        $logger->error((string)$error);
        // create bulk_items/1 failed with 409: version conflict, document already exists (...)
        $error->getAction();   // 'create'
        $error->getStatus();   // 409
        $error->getId();
    }
}

$response->count();            // celkem položek
$response->getSuccessCount();  // úspěšných
$response->getTook();          // ms
```

Velikost dávky si řídí volající — knihovna dávku nedělí. Elasticsearch doporučuje řádově
tisíce dokumentů nebo 5–15 MB těla na jeden request.

[]() > [Ukázka použítí](../../../examples/indexData.php) <

[<< zpět](../../../README.md)
