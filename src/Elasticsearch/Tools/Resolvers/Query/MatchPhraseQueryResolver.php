<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class MatchPhraseQueryResolver extends AbstractQueryResolver
{
    use FieldQueryResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        [$field, $options] = $this->resolveFieldMetadata($metadata, 'query', 'Match phrase');
        $property ??= '$matchPhraseQuery';

        $arguments = [
            sprintf('field: %s', $this->resolveValue($field)),
            sprintf('query: %s', $this->resolveValue($options['query'])),
        ];
        $arguments = array_merge(
            $arguments,
            $this->resolveNamedArguments($options, ['analyzer', 'slop', 'zero_terms_query', 'boost'])
        );

        return sprintf('%s = new MatchPhraseQuery(%s);', $property, implode(', ', $arguments));
    }
}
