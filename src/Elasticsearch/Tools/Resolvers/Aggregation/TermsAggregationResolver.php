<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class TermsAggregationResolver extends AbstractAggregationResolver
{
    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        $lines = [
            sprintf(
                '%s = new TermsAggregation(%s, %s);',
                $property,
                $this->resolvePhpValue($name),
                $this->resolvePhpValue($metadata['field'] ?? '')
            ),
        ];

        if (isset($metadata['size'])) {
            $lines[] = sprintf('%s->size(%s);', $property, $this->resolvePhpValue((int) $metadata['size']));
        }

        if (isset($metadata['order']) && is_array($metadata['order'])) {
            $lines[] = sprintf('%s->order(%s);', $property, $this->resolvePhpValue($metadata['order']));
        }

        if (isset($metadata['missing'])) {
            $lines[] = sprintf('%s->missing(%s);', $property, $this->resolvePhpValue($metadata['missing']));
        }

        if (isset($metadata['shard_size'])) {
            $lines[] = sprintf(
                '%s->shardSize(%s);',
                $property,
                $this->resolvePhpValue((int) $metadata['shard_size'])
            );
        }

        if (isset($metadata['min_doc_count'])) {
            $lines[] = sprintf(
                '%s->minDocCount(%s);',
                $property,
                $this->resolvePhpValue((int) $metadata['min_doc_count'])
            );
        }

        foreach (['include', 'exclude'] as $key) {
            if (isset($metadata[$key])) {
                $lines[] = sprintf('%s->%s(%s);', $property, $key, $this->resolvePhpValue($metadata[$key]));
            }
        }

        return $lines;
    }
}
