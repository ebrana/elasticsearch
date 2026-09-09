<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class WildcardQueryResolver extends AbstractQueryResolver
{
    use BoostTrait;
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        $field = array_key_first($metadata);
        $property = $property ?? '$wildcardQuery';

        if (null === $field) {
            throw new \RuntimeException('Wildcard query must contain field metadata.');
        }

        $value = $metadata[$field];

        if (!is_array($value)) {
            throw new \RuntimeException('Wildcard query metadata for field must be array.');
        }

        $result = sprintf(
            '%s = new WildcardQuery(field: %s, value: %s',
            $property,
            $this->resolveValue($field),
            $this->resolveValue($value['value'] ?? null)
        );
        $boost = $this->resolveBoost($value);

        if ($boost) {
            $result .= ', boost: ' . $boost;
        }
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
