<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

use RuntimeException;

class MoreLikeThisQueryResolver extends AbstractQueryResolver
{
    use FieldQueryResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        if (!isset($metadata['fields'])) {
            throw new RuntimeException('More like this query must have fields property.');
        }
        if (!isset($metadata['like'])) {
            throw new RuntimeException('More like this query must have like property.');
        }

        $property ??= '$moreLikeThisQuery';
        $arguments = [
            sprintf('fields: %s', $this->resolveValue($metadata['fields'])),
            sprintf('like: %s', $this->resolveValue($metadata['like'])),
        ];
        $arguments = array_merge(
            $arguments,
            $this->resolveNamedArguments(
                $metadata,
                [
                    'unlike',
                    'min_term_freq',
                    'max_query_terms',
                    'min_doc_freq',
                    'max_doc_freq',
                    'min_word_length',
                    'max_word_length',
                    'stop_words',
                    'analyzer',
                    'minimum_should_match',
                    'boost_terms',
                    'include',
                    'fail_on_unsupported_field',
                    'boost',
                ]
            )
        );

        return sprintf('%s = new MoreLikeThisQuery(%s);', $property, implode(', ', $arguments));
    }
}
