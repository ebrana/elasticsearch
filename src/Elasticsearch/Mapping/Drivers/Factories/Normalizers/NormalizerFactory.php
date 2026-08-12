<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Normalizers;

use Elasticsearch\Mapping\Settings\Normalizer;
use stdClass;

final class NormalizerFactory implements NormalizerFactoryInterface
{
    /**
     * Cte filter a char_filter; Elasticsearch u obou pripousti jak jedno jmeno, tak pole jmen.
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
