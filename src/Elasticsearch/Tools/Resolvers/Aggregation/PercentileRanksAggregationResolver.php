<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class PercentileRanksAggregationResolver extends AbstractAggregationResolver
{
    use MetricAggregationResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        $lines = [
            sprintf(
                '%s = new PercentileRanksAggregation(%s, %s, %s);',
                $property,
                $this->resolvePhpValue($name),
                $this->resolvePhpValue($metadata['field'] ?? ''),
                $this->resolvePhpValue($metadata['values'] ?? [])
            ),
        ];

        return array_merge(
            $lines,
            $this->resolveOptions($metadata, $property, ['keyed' => 'keyed', 'missing' => 'missing'])
        );
    }
}
