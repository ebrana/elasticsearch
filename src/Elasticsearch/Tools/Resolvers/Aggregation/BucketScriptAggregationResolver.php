<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class BucketScriptAggregationResolver extends AbstractAggregationResolver
{
    use PipelineAggregationResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        $lines = [
            sprintf(
                '%s = new BucketScriptAggregation(%s, %s, %s);',
                $property,
                $this->resolvePhpValue($name),
                $this->resolvePhpValue($metadata['buckets_path'] ?? []),
                $this->resolvePhpValue($metadata['script'] ?? '')
            ),
        ];

        return array_merge($lines, $this->resolvePipelineOptions($metadata, $property));
    }
}
