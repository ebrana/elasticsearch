<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class MultiMatchQueryResolver extends AbstractQueryResolver
{
    use MatchQueryTrait;
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        $query = $metadata['query'] ?? null;
        $property = $property ?? '$multiMatchQuery';
        $fields = $metadata['fields'] ?? null;
        if (!is_string($query) || !is_array($fields)) {
            throw new \RuntimeException('Unresolved query.');
        }
        $fields = $this->resolveValue($fields);

        $result = sprintf('%s = new MultiMatchQuery(query: %s, fields: %s', $property, $this->resolveValue($query), $fields);

        if (isset($metadata['type'])) {
            $type = match($metadata['type']) {
                'best_fields' => 'MultiMatchType::BEST_FIELDS',
                'most_fields' => 'MultiMatchType::MOST_FIELDS',
                'cross_fields' => 'MultiMatchType::CROSS_FIELDS',
                'phrase_prefix' => 'MultiMatchType::PHRASE_PREFIX',
                'bool_prefix' => 'MultiMatchType::BOOL_PREFIX',
                'phrase' => 'MultiMatchType::PHRASE',
                default => throw new \RuntimeException('Unsupported multi_match type.'),
            };
            $result .= ', type: ' . $type;
        }

        $result .= ');';

        $result .= $this->resolveMatch($property, $metadata);

        return $result;
    }
}
