<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class ExtendedStatsAggregationResolver extends AbstractAggregationResolver
{
    use MetricAggregationResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        $lines = $this->resolveMetric('ExtendedStatsAggregation', $name, $metadata, $property);

        return array_merge($lines, $this->resolveOptions($metadata, $property, ['sigma' => 'sigma', 'missing' => 'missing']));
    }
}
