<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class MinAggregationResolver extends AbstractAggregationResolver
{
    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        $lines = [
            sprintf(
                '%s = new MinAggregation(%s, %s);',
                $property,
                $this->resolvePhpValue($name),
                $this->resolvePhpValue($metadata['field'] ?? '')
            ),
        ];

        if (isset($metadata['missing'])) {
            $lines[] = sprintf('%s->missing(%s);', $property, $this->resolvePhpValue($metadata['missing']));
        }

        return $lines;
    }
}
