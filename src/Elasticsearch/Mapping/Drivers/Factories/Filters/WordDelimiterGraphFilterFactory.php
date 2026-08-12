<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Settings\Filters\WordDelimiterGraphFilter;
use stdClass;

class WordDelimiterGraphFilterFactory implements FilterFactoryInterface
{
    /**
     * @param stdClass&object{
     *     adjust_offsets?: bool,
     *     catenate_all?: bool,
     *     catenate_numbers?: bool,
     *     catenate_words?: bool,
     *     generate_number_parts?: bool,
     *     generate_word_parts?: bool,
     *     ignore_keywords?: bool,
     *     preserve_original?: bool,
     *     split_on_case_change?: bool,
     *     split_on_numerics?: bool,
     *     stem_english_possessive?: bool,
     *     protected_words?: string[],
     *     protected_words_path?: string,
     *     type_table?: string[],
     *     type_table_path?: string
     * } $configuration
     */
    public static function create(string $name, stdClass $configuration): WordDelimiterGraphFilter
    {
        return new WordDelimiterGraphFilter(
            $name,
            (bool)($configuration->adjust_offsets ?? true),
            (bool)($configuration->catenate_all ?? false),
            (bool)($configuration->catenate_numbers ?? false),
            (bool)($configuration->catenate_words ?? false),
            (bool)($configuration->generate_number_parts ?? true),
            (bool)($configuration->generate_word_parts ?? true),
            (bool)($configuration->ignore_keywords ?? false),
            (bool)($configuration->preserve_original ?? false),
            (bool)($configuration->split_on_case_change ?? true),
            (bool)($configuration->split_on_numerics ?? true),
            (bool)($configuration->stem_english_possessive ?? true),
            $configuration->protected_words ?? null,
            $configuration->protected_words_path ?? null,
            $configuration->type_table ?? null,
            $configuration->type_table_path ?? null
        );
    }
}
