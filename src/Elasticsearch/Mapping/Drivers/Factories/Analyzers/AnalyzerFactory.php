<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Analyzers;

use Elasticsearch\Mapping\Exceptions\AttributeMissingException;
use Elasticsearch\Mapping\Settings\Analyzer;
use stdClass;

final class AnalyzerFactory implements AnalyzerFactoryInterface
{
    /**
     * @param stdClass&object{tokenizer?: string, filter?: string[]|null|scalar, char_filter?: string[]|null|scalar} $configuration
     * @throws \Elasticsearch\Mapping\Exceptions\AttributeMissingException
     */
    public static function create(string $name, stdClass $configuration): Analyzer
    {
        if (!isset($configuration->tokenizer)) {
            throw new AttributeMissingException('Analyzer must define tokenizer.');
        }
        $filters = self::resolveList($configuration->filter ?? null);
        $charFilters = self::resolveList($configuration->char_filter ?? null);

        return new Analyzer($name, $configuration->tokenizer, $filters, $charFilters);
    }

    /**
     * Elasticsearch accepts both a single name and a list of names.
     *
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
