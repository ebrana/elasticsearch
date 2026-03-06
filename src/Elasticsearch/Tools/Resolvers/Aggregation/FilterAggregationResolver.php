<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class FilterAggregationResolver extends AbstractAggregationResolver
{
    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        $filterProperty = '$aggregationFilterQuery' . $this->aggregationResolver->nextId();
        $resolvedFilter = $this->aggregationResolver->resolveQuery($metadata, $filterProperty);

        $lines = [];
        if ('' !== $resolvedFilter) {
            $lines[] = $resolvedFilter;
        }
        $lines[] = sprintf(
            '%s = new FilterAggregation(%s, %s);',
            $property,
            $this->resolvePhpValue($name),
            $filterProperty
        );

        return $lines;
    }
}
