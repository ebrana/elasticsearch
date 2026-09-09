<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class BucketSortAggregationResolver extends AbstractAggregationResolver
{
    use MetricAggregationResolverTrait;
    use PipelineAggregationResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        $lines = [
            sprintf('%s = new BucketSortAggregation(%s);', $property, $this->resolvePhpValue($name)),
        ];

        if (isset($metadata['sort']) && is_array($metadata['sort'])) {
            foreach ($metadata['sort'] as $sort) {
                $lines[] = sprintf('%s->sort(%s);', $property, $this->resolvePhpValue($sort));
            }
        }

        $lines = array_merge($lines, $this->resolveOptions($metadata, $property, [
            'from' => 'from',
            'size' => 'size',
        ]));

        return array_merge($lines, $this->resolvePipelineOptions($metadata, $property));
    }
}
