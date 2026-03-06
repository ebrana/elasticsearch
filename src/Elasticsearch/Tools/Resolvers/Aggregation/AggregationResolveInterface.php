<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

interface AggregationResolveInterface
{
    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array;
}
