<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class MatchPhrasePrefixQueryResolver extends AbstractQueryResolver
{
    use FieldQueryResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        [$field, $options] = $this->resolveFieldMetadata($metadata, 'query', 'Match phrase prefix');
        $property ??= '$matchPhrasePrefixQuery';

        $arguments = [
            sprintf('field: %s', $this->resolveValue($field)),
            sprintf('query: %s', $this->resolveValue($options['query'])),
        ];
        $arguments = array_merge(
            $arguments,
            $this->resolveNamedArguments(
                $options,
                ['analyzer', 'max_expansions', 'slop', 'zero_terms_query', 'boost']
            )
        );

        return sprintf('%s = new MatchPhrasePrefixQuery(%s);', $property, implode(', ', $arguments));
    }
}
