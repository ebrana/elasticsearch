<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class RangeAggregationResolver extends AbstractAggregationResolver
{
    use MetricAggregationResolverTrait;
    use RangeResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        $lines = [
            sprintf(
                '%s = new RangeAggregation(%s, %s%s);',
                $property,
                $this->resolvePhpValue($name),
                $this->resolvePhpValue($metadata['field'] ?? ''),
                $this->resolveRanges($metadata)
            ),
        ];

        return array_merge($lines, $this->resolveOptions($metadata, $property, [
            'keyed'   => 'keyed',
            'missing' => 'missing',
        ]));
    }
}
