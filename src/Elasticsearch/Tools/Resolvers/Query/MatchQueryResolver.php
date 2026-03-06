<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class MatchQueryResolver extends AbstractQueryResolver
{
    use ValueResolverTrait;
    use MatchQueryTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        $field = array_key_first($metadata);
        $property = $property ?? '$matchQuery';

        if (null === $field) {
            throw new \RuntimeException('Match query must contain field metadata.');
        }

        $fieldMetadata = $metadata[$field];
        $query = $fieldMetadata;
        $options = [];

        if (is_array($fieldMetadata)) {
            $query = $fieldMetadata['query'] ?? null;
            $options = $fieldMetadata;
        }

        if (null === $query) {
            throw new \RuntimeException('Match query must contain query value.');
        }

        $result = sprintf(
            '%s = new MatchQuery(field: %s, query: %s);',
            $property,
            $this->resolveValue($field),
            $this->resolveValue($query)
        );
        $result .= $this->resolveMatch($property, $options);

        return $result;
    }
}
