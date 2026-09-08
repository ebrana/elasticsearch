<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

use RuntimeException;

/**
 * Shared logic for queries shaped {"<type>": {"<field>": {...options}}}. Elasticsearch also
 * accepts the shorthand {"<type>": {"<field>": "<value>"}}, so a scalar is converted to an
 * array under the given key.
 */
trait FieldQueryResolverTrait
{
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     * @return array{0: string, 1: array<string, mixed>}
     */
    private function resolveFieldMetadata(array $metadata, string $valueKey, string $queryName): array
    {
        $field = array_key_first($metadata);
        if (null === $field) {
            throw new RuntimeException(sprintf('%s query must contain field metadata.', $queryName));
        }

        $options = $metadata[$field];
        if (!is_array($options)) {
            $options = [$valueKey => $options];
        }

        if (!isset($options[$valueKey])) {
            throw new RuntimeException(sprintf('%s query must contain %s value.', $queryName, $valueKey));
        }

        /** @var array<string, mixed> $options */
        return [(string)$field, $options];
    }

    /**
     * Builds the list of named arguments from those keys that are present in the data.
     *
     * @param array<string, mixed> $options
     * @param string[] $keys
     * @return string[]
     */
    private function resolveNamedArguments(array $options, array $keys): array
    {
        $arguments = [];

        foreach ($keys as $key) {
            if (isset($options[$key])) {
                $arguments[] = sprintf('%s: %s', $key, $this->resolveValue($options[$key]));
            }
        }

        return $arguments;
    }
}
