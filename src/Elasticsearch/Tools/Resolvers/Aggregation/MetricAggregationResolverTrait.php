<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

/**
 * The metrics share the same shape: a constructor (name, field) and optional fluent options.
 */
trait MetricAggregationResolverTrait
{
    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    private function resolveMetric(string $class, string $name, array $metadata, string $property): array
    {
        return [
            sprintf(
                '%s = new %s(%s, %s);',
                $property,
                $class,
                $this->resolvePhpValue($name),
                $this->resolvePhpValue($metadata['field'] ?? '')
            ),
        ];
    }

    /**
     * @param array<string, mixed> $metadata
     * @param array<string, string> $options metadata key => fluent method name
     * @return string[]
     */
    private function resolveOptions(array $metadata, string $property, array $options): array
    {
        $lines = [];

        foreach ($options as $key => $method) {
            if (isset($metadata[$key])) {
                $lines[] = sprintf('%s->%s(%s);', $property, $method, $this->resolvePhpValue($metadata[$key]));
            }
        }

        return $lines;
    }
}
