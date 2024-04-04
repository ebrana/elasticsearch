<?php

declare(strict_types=1);

namespace Elasticsearch\Tests\Builders;

use Elasticsearch\Indexing\Document;
use Elasticsearch\Indexing\Interfaces\DocumentBuilderInterface;
use Elasticsearch\Indexing\Interfaces\DocumentInterface;
use Elasticsearch\Indexing\Interfaces\IndexableEntityInterface;
use Elasticsearch\Indexing\Resolvers\CollectionByKeyResolverTrait;
use Elasticsearch\Indexing\Resolvers\ScalarValueResolverTrait;
use Elasticsearch\Mapping\Exceptions\EmptyIndexNameException;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Tests\Entity\Product;
use Elasticsearch\Tests\Entity\Translations;
use Elasticsearch\Indexing\Exceptions\DocumentToJsonException;

final class ProductDocumentBuilder implements DocumentBuilderInterface
{
    use ScalarValueResolverTrait;
    use CollectionByKeyResolverTrait;

    public function __construct(private readonly Index $index)
    {
    }

    /**
     * @param Product $entity
     * @throws DocumentToJsonException
     */
    public function build(IndexableEntityInterface $entity): DocumentInterface
    {
        $document = new Document($this->index, $entity->getPk());
        $this->resolveScalarByMetadata($document, $entity, $this->index);
        $this->resolveCollectionsByMetadata($document, $entity, $this->index, function (Translations $entity) {
            return '@' . $entity->getLang();
        });
        $document->set('sellingPriceWithVatKeyword', 'test');
        $document->set('test1', [
            '@cs' => null,
            '@en' => null,
            '@sk' => null,
        ]);
        $document->set('test2', [
            '@cs' => null,
            '@en' => null,
            '@sk' => null,
        ]);
//        $this->resolveSellingPrice($entity, $document);

        return $document;
    }

//    private function resolveSellingPrice(Product $entity, DocumentInterface $document): void
//    {
//        $sellingPriceObject = [];
//        $sellingPriceType = $this->index->getProperties()->get('sellingPrice');
//
//        if ($sellingPriceType) {
//            /** @var \Elasticsearch\Tests\Entity\Translations $item */
//            foreach ($entity->getSellingPrice() as $item) {
//                $sellingPriceObject['@' . $item->getLang()] = $item->getValue();
//            }
//            $document->set('sellingPrice', $sellingPriceObject);
//        }
//    }
}
