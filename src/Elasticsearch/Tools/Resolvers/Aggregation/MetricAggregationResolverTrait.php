<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

/**
 * Metriky maji stejny tvar: konstruktor (jmeno, pole) a volitelne fluent volby.
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
     * @param array<string, string> $options klic v metadatech => jmeno fluent metody
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
