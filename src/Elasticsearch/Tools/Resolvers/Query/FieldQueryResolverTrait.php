<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

use RuntimeException;

/**
 * Sdilena logika pro query ve tvaru {"<typ>": {"<pole>": {...volby}}}. Elasticsearch
 * pripousti i zkraceny zapis {"<typ>": {"<pole>": "<hodnota>"}}, proto se skalar
 * prevadi na pole pod zadanym klicem.
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
     * Slozi seznam pojmenovanych argumentu z tech klicu, ktere jsou v datech pritomne.
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
