<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Mapping\Drivers\Resolvers\KeyResolver\KeyCollection;
use Elasticsearch\Mapping\Drivers\Resolvers\KeyResolver\KeyResolverInterface;

class CustomKeyResolver implements KeyResolverInterface
{
    public function resolve(): KeyCollection
    {
        $collection = new KeyCollection();
        $collection->add('field1');
        $collection->add('field2');

        return $collection;
    }
}
