<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class FuzzyQueryResolver extends AbstractQueryResolver
{
    use FieldQueryResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        [$field, $options] = $this->resolveFieldMetadata($metadata, 'value', 'Fuzzy');
        $property ??= '$fuzzyQuery';

        $arguments = [
            sprintf('field: %s', $this->resolveValue($field)),
            sprintf('value: %s', $this->resolveValue($options['value'])),
        ];
        $arguments = array_merge(
            $arguments,
            $this->resolveNamedArguments(
                $options,
                ['fuzziness', 'max_expansions', 'prefix_length', 'transpositions', 'rewrite', 'boost']
            )
        );

        return sprintf('%s = new FuzzyQuery(%s);', $property, implode(', ', $arguments));
    }
}
