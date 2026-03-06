<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class CardinalityAggregationResolver extends AbstractAggregationResolver
{
    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        $constructor = sprintf(
            '%s = new CardinalityAggregation(%s, %s',
            $property,
            $this->resolvePhpValue($name),
            $this->resolvePhpValue($metadata['field'] ?? '')
        );

        if (isset($metadata['precision_threshold'])) {
            $constructor .= sprintf(', precision_threshold: %s', $this->resolvePhpValue((int) $metadata['precision_threshold']));
        }
        $constructor .= ');';

        $lines = [$constructor];

        if (isset($metadata['missing'])) {
            $lines[] = sprintf('%s->missing(%s);', $property, $this->resolvePhpValue($metadata['missing']));
        }

        return $lines;
    }
}
