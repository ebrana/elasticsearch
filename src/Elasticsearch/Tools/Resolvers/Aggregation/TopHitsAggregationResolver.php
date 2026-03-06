<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class TopHitsAggregationResolver extends AbstractAggregationResolver
{
    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        $size = isset($metadata['size']) ? (int) $metadata['size'] : 1;
        $lines = [];
        $constructor = sprintf('%s = new TopHitsAggregation(%s, size: %d', $property, $this->resolvePhpValue($name), $size);

        if (isset($metadata['sort'][0]) && is_array($metadata['sort']) && is_array($metadata['sort'][0])) {
            $sortProperty = '$aggregationSort' . $this->aggregationResolver->nextId();
            [$sortLines, $resolvedSortProperty] = $this->aggregationResolver->resolveSingleSort($metadata['sort'][0], $sortProperty);
            foreach ($sortLines as $line) {
                $lines[] = $line;
            }
            $constructor .= sprintf(', sort: %s', $resolvedSortProperty);
        }

        $constructor .= ');';
        $lines[] = $constructor;

        if (isset($metadata['_source'])) {
            $lines[] = sprintf('%s->setSource(%s);', $property, $this->resolvePhpValue($metadata['_source']));
        }

        return $lines;
    }
}
