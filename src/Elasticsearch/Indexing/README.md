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
      function (AmproductsModuleLangs $langs) {
            return '@' . $langs->getLang();
      }
);
`````

[]() > [Ukázka použítí](../../../examples/indexData.php) <

[<< zpět](../../../README.md)
