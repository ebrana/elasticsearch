<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class PrefixQueryResolver extends AbstractQueryResolver
{
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        $field = array_key_first($metadata);
        $property = $property ?? '$prefixQuery';

        if (null === $field) {
            throw new \RuntimeException('Prefix query must contain field metadata.');
        }

        $value = $metadata[$field];

        if (!is_array($value)) {
            $value = ['value' => $value];
        }

        $result = sprintf(
            '%s = new PrefixQuery(field: %s, value: %s',
            $property,
            $this->resolveValue($field),
            $this->resolveValue($value['value'] ?? null)
        );
        if (isset($value['rewrite'])) {
            $result .= sprintf(', rewrite: %s', $this->resolveValue($value['rewrite']));
        }
        if (isset($value['case_insensitive'])) {
            $result .= ', case_insensitive: ' . ($value['case_insensitive'] ? 'true' : 'false');
        }

        $result .= ');';

        return $result;
    }
}
