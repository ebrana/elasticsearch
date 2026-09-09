<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class MultiTermsAggregationResolver extends AbstractAggregationResolver
{
    use MetricAggregationResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        $fields = [];
        if (isset($metadata['terms']) && is_array($metadata['terms'])) {
            foreach ($metadata['terms'] as $term) {
                if (is_array($term) && isset($term['field'])) {
                    $fields[] = $this->resolvePhpValue($term['field']);
                }
            }
        }

        $lines = [
            sprintf(
                '%s = new MultiTermsAggregation(%s%s);',
                $property,
                $this->resolvePhpValue($name),
                $fields ? ', ' . implode(', ', $fields) : ''
            ),
        ];

        return array_merge($lines, $this->resolveOptions($metadata, $property, [
            'size'          => 'size',
            'shard_size'    => 'shardSize',
            'min_doc_count' => 'minDocCount',
            'order'         => 'order',
        ]));
    }
}
