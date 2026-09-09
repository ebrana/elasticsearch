<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class SignificantTermsAggregationResolver extends AbstractAggregationResolver
{
    use MetricAggregationResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        $lines = $this->resolveMetric('SignificantTermsAggregation', $name, $metadata, $property);

        return array_merge($lines, $this->resolveOptions($metadata, $property, [
            'size' => 'size',
            'shard_size' => 'shardSize',
            'min_doc_count' => 'minDocCount',
            'include' => 'include',
            'exclude' => 'exclude',
        ]));
    }
}
