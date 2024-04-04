<?php declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Mapping\Drivers\Resolvers\KeyResolver\KeyCollection;
use Elasticsearch\Mapping\Drivers\Resolvers\KeyResolver\KeyResolverInterface;

class LangKeyResolver implements KeyResolverInterface
{
    public function resolve(): KeyCollection
    {
        $collection = new KeyCollection();
        $collection->add('@cs');
        $collection->add('@en');
        $collection->add('@de');
        $collection->add('@fr');

        return $collection;
    }
}
