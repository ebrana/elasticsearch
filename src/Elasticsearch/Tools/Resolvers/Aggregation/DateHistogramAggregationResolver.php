<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class DateHistogramAggregationResolver extends AbstractAggregationResolver
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
                '%s = new DateHistogramAggregation(%s, %s);',
                $property,
                $this->resolvePhpValue($name),
                $this->resolvePhpValue($metadata['field'] ?? '')
            ),
        ];

        return array_merge($lines, $this->resolveOptions($metadata, $property, [
            'calendar_interval' => 'calendarInterval',
            'fixed_interval'    => 'fixedInterval',
            'format'            => 'format',
            'time_zone'         => 'timeZone',
            'offset'            => 'offset',
            'min_doc_count'     => 'minDocCount',
            'keyed'             => 'keyed',
            'order'             => 'order',
            'missing'           => 'missing',
        ]));
    }
}
