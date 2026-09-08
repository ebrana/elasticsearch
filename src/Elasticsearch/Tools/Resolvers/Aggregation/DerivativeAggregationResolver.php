<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class DerivativeAggregationResolver extends AbstractAggregationResolver
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
                '%s = new DerivativeAggregation(%s, %s);',
                $property,
                $this->resolvePhpValue($name),
                $this->resolvePhpValue($metadata['buckets_path'] ?? '')
            ),
        ];

        if (isset($metadata['unit'])) {
            $lines[] = sprintf('%s->unit(%s);', $property, $this->resolvePhpValue($metadata['unit']));
        }

        return array_merge($lines, $this->resolvePipelineOptions($metadata, $property));
    }
}
