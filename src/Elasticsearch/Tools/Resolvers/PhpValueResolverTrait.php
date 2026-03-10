<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers;

trait PhpValueResolverTrait
{
    protected function resolvePhpValue(mixed $value): string
    {
        if (is_string($value)) {
            return sprintf("'%s'", addslashes($value));
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_int($value)) {
            return (string) $value;
        }

        if (is_float($value)) {
            $formatted = rtrim(rtrim(sprintf('%.12F', $value), '0'), '.');

            return str_contains($formatted, '.') ? $formatted : $formatted . '.0';
        }

        if (is_array($value)) {
            if (array_is_list($value)) {
                $items = array_map(fn (mixed $item): string => $this->resolvePhpValue($item), $value);

                return '[' . implode(', ', $items) . ']';
            }

            $items = [];
            foreach ($value as $key => $item) {
                $items[] = sprintf('%s => %s', $this->resolvePhpValue((string) $key), $this->resolvePhpValue($item));
            }

            return '[' . implode(', ', $items) . ']';
        }

        if (null === $value) {
            return 'null';
        }

        return sprintf("'%s'", addslashes((string) $value));
    }
}
