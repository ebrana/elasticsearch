<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Resolvers\KeyResolver;

interface KeyResolverInterface
{
    /**
     * @return KeyCollection<string>
     */
    public function resolve(): KeyCollection;
}
