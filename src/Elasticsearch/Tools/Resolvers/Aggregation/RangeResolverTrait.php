<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

trait RangeResolverTrait
{
    /**
     * Slozi variadicke argumenty `new Range(...)` pro range a date_range agregaci.
     *
     * @param array<string, mixed> $metadata
     */
    private function resolveRanges(array $metadata): string
    {
        if (!isset($metadata['ranges']) || !is_array($metadata['ranges'])) {
            return '';
        }

        $ranges = [];
        foreach ($metadata['ranges'] as $range) {
            if (!is_array($range)) {
                continue;
            }

            $arguments = [];
            foreach (['from', 'to', 'key'] as $key) {
                if (isset($range[$key])) {
                    $arguments[] = sprintf('%s: %s', $key, $this->resolvePhpValue($range[$key]));
                }
            }

            $ranges[] = sprintf('new Range(%s)', implode(', ', $arguments));
        }

        return $ranges ? ', ' . implode(', ', $ranges) : '';
    }
}
