<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Normalizers;

use Elasticsearch\Mapping\Settings\Normalizer;
use stdClass;

final class NormalizerFactory implements NormalizerFactoryInterface
{
    /**
     * Reads filter and char_filter; Elasticsearch accepts both a single name and an array of names for each.
     */
    public static function create(string $name, stdClass $configuration): Normalizer
    {
        return new Normalizer(
            $name,
            self::resolveList($configuration->filter ?? null),
            self::resolveList($configuration->char_filter ?? null)
        );
    }

    /**
     * @param string[]|null|scalar $value
     * @return string[]
     */
    private static function resolveList(array|string|int|float|bool|null $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        return is_string($value) && '' !== $value ? [$value] : [];
    }
}
