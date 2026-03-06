<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class NestedAggregationResolver extends AbstractAggregationResolver
{
    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        return [
            sprintf(
                '%s = new NestedAggregation(%s, %s);',
                $property,
                $this->resolvePhpValue($name),
                $this->resolvePhpValue($metadata['path'] ?? '')
            ),
        ];
    }
}
