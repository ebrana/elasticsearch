<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

use RuntimeException;

class SimpleQueryStringQueryResolver extends AbstractQueryResolver
{
    use FieldQueryResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        if (!isset($metadata['query'])) {
            throw new RuntimeException('Simple query string query must contain query value.');
        }

        $property ??= '$simpleQueryStringQuery';
        $arguments = [sprintf('query: %s', $this->resolveValue($metadata['query']))];

        if (isset($metadata['fields'])) {
            $arguments[] = sprintf('fields: %s', $this->resolveValue($metadata['fields']));
        }

        if (isset($metadata['flags'])) {
            $flags = array_map(
                static fn (string $flag): string => 'SimpleQueryStringFlag::' . strtoupper(trim($flag)),
                explode('|', (string)$metadata['flags'])
            );
            $arguments[] = sprintf('flags: [%s]', implode(', ', $flags));
        }

        if (isset($metadata['default_operator'])) {
            $arguments[] = sprintf(
                'default_operator: Operator::%s',
                strtoupper((string)$metadata['default_operator'])
            );
        }

        $arguments = array_merge(
            $arguments,
            $this->resolveNamedArguments(
                $metadata,
                [
                    'analyzer',
                    'minimum_should_match',
                    'fuzzy_max_expansions',
                    'fuzzy_prefix_length',
                    'fuzzy_transpositions',
                    'lenient',
                    'analyze_wildcard',
                    'auto_generate_synonyms_phrase_query',
                    'quote_field_suffix',
                    'boost',
                ]
            )
        );

        return sprintf('%s = new SimpleQueryStringQuery(%s);', $property, implode(', ', $arguments));
    }
}
