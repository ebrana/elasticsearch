<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

abstract class AbstractQueryResolver implements QueryResolveInterface
{
    public function __construct(protected QueryResolver $queryResolver)
    {
    }
}
