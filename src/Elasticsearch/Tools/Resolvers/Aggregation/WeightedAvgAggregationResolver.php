<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class WeightedAvgAggregationResolver extends AbstractAggregationResolver
{
    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        /** @var array<string, mixed> $value */
        $value = is_array($metadata['value'] ?? null) ? $metadata['value'] : [];
        /** @var array<string, mixed> $weight */
        $weight = is_array($metadata['weight'] ?? null) ? $metadata['weight'] : [];

        $lines = [
            sprintf(
                '%s = new WeightedAvgAggregation(%s, %s, %s);',
                $property,
                $this->resolvePhpValue($name),
                $this->resolvePhpValue($value['field'] ?? ''),
                $this->resolvePhpValue($weight['field'] ?? '')
            ),
        ];

        if (isset($value['missing'])) {
            $lines[] = sprintf('%s->valueMissing(%s);', $property, $this->resolvePhpValue($value['missing']));
        }

        if (isset($weight['missing'])) {
            $lines[] = sprintf('%s->weightMissing(%s);', $property, $this->resolvePhpValue($weight['missing']));
        }

        return $lines;
    }
}
