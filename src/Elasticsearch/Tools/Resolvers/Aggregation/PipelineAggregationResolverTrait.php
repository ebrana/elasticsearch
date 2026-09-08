<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

trait PipelineAggregationResolverTrait
{
    /**
     * gap_policy je enum, format obycejny retezec.
     *
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    private function resolvePipelineOptions(array $metadata, string $property): array
    {
        $lines = [];

        if (isset($metadata['gap_policy'])) {
            $lines[] = sprintf(
                '%s->gapPolicy(GapPolicy::%s);',
                $property,
                strtoupper((string)$metadata['gap_policy'])
            );
        }

        if (isset($metadata['format'])) {
            $lines[] = sprintf('%s->format(%s);', $property, $this->resolvePhpValue($metadata['format']));
        }

        return $lines;
    }
}
