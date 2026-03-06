<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

use Elasticsearch\Tools\Resolvers\AggregationResolver;
use Elasticsearch\Tools\Resolvers\PhpValueResolverTrait;

abstract class AbstractAggregationResolver implements AggregationResolveInterface
{
    use PhpValueResolverTrait;

    public function __construct(protected AggregationResolver $aggregationResolver)
    {
    }
}
