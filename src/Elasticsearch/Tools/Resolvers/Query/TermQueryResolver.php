<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class TermQueryResolver extends AbstractQueryResolver
{
    use ValueResolverTrait;
    use BoostTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        $field = null;
        foreach ($metadata as $key => $value) {
            if ($key !== 'boost') {
                $field = $key;
                break;
            }
        }

        if (null === $field) {
            throw new \RuntimeException('Term query must contain field metadata.');
        }

        $valueMetadata = $metadata[$field];
        $boost = $this->resolveBoost($metadata);

        if (is_array($valueMetadata) && array_key_exists('value', $valueMetadata)) {
            $value = $this->resolveValue($valueMetadata['value']);
            $boost = $boost ?? $this->resolveBoost($valueMetadata);
        } else {
            $value = $this->resolveValue($valueMetadata);
        }

        $property = $property ?? '$termQuery';
        $result = sprintf(
            '%s = new TermQuery(field: %s, value: %s',
            $property,
            $this->resolveValue($field),
            $value
        );

        if (null !== $boost) {
            $result .= sprintf(', boost: %s', $boost);
        }

        $result .= ');';

        return $result;
    }
}
