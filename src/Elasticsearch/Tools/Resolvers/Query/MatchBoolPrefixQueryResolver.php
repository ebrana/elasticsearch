<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class MatchBoolPrefixQueryResolver extends AbstractQueryResolver
{
    use FieldQueryResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        [$field, $options] = $this->resolveFieldMetadata($metadata, 'query', 'Match bool prefix');
        $property ??= '$matchBoolPrefixQuery';

        $arguments = [
            sprintf('field: %s', $this->resolveValue($field)),
            sprintf('query: %s', $this->resolveValue($options['query'])),
        ];

        if (isset($options['operator'])) {
            $arguments[] = sprintf('operator: Operator::%s', strtoupper((string)$options['operator']));
        }

        $arguments = array_merge(
            $arguments,
            $this->resolveNamedArguments(
                $options,
                [
                    'analyzer',
                    'minimum_should_match',
                    'fuzziness',
                    'prefix_length',
                    'max_expansions',
                    'fuzzy_transpositions',
                    'fuzzy_rewrite',
                    'boost',
                ]
            )
        );

        return sprintf('%s = new MatchBoolPrefixQuery(%s);', $property, implode(', ', $arguments));
    }
}
