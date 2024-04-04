<?php

declare(strict_types=1);

namespace Elasticsearch\Tests\Builders;

use Elasticsearch\Indexing\Interfaces\DocumentBuilderFactoryInterface;
use Elasticsearch\Indexing\Interfaces\DocumentBuilderInterface;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Tests\Entity\Product;

final class ProductDocumentBuilderFactory implements DocumentBuilderFactoryInterface
{
    public function create(Index $index): DocumentBuilderInterface
    {
        return new ProductDocumentBuilder($index);
    }

    public static function getEntityClass(): string
    {
        return Product::class;
    }
}
