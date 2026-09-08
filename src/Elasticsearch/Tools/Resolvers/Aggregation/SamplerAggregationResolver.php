<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class SamplerAggregationResolver extends AbstractAggregationResolver
{
    use MetricAggregationResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        $lines = [
            sprintf('%s = new SamplerAggregation(%s);', $property, $this->resolvePhpValue($name)),
        ];

        return array_merge($lines, $this->resolveOptions($metadata, $property, [
            'shard_size' => 'shardSize',
        ]));
    }
}
