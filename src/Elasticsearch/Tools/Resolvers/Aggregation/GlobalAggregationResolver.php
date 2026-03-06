<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class GlobalAggregationResolver extends AbstractAggregationResolver
{
    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        return [
            sprintf('%s = new GlobalAggregation(%s);', $property, $this->resolvePhpValue($name)),
        ];
    }
}
