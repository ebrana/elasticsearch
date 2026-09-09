<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class TermsSetQueryResolver extends AbstractQueryResolver
{
    use FieldQueryResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        [$field, $options] = $this->resolveFieldMetadata($metadata, 'terms', 'Terms set');
        $property ??= '$termsSetQuery';

        $arguments = [
            sprintf('field: %s', $this->resolveValue($field)),
            sprintf('terms: %s', $this->resolveValue($options['terms'])),
        ];
        $arguments = array_merge(
            $arguments,
            $this->resolveNamedArguments(
                $options,
                [
                    'minimum_should_match_field',
                    'minimum_should_match_script',
                    'minimum_should_match',
                    'boost',
                ]
            )
        );

        return sprintf('%s = new TermsSetQuery(%s);', $property, implode(', ', $arguments));
    }
}
