<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class HistogramAggregationResolver extends AbstractAggregationResolver
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
                '%s = new HistogramAggregation(%s, %s, %s);',
                $property,
                $this->resolvePhpValue($name),
                $this->resolvePhpValue($metadata['field'] ?? ''),
                $this->resolvePhpValue($metadata['interval'] ?? 0)
            ),
        ];

        return array_merge($lines, $this->resolveOptions($metadata, $property, [
            'min_doc_count' => 'minDocCount',
            'offset' => 'offset',
            'keyed' => 'keyed',
            'order' => 'order',
            'missing' => 'missing',
        ]));
    }
}
