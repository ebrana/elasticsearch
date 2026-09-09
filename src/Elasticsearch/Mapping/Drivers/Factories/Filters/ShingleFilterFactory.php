<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Settings\Filters\ShingleFilter;
use stdClass;

class ShingleFilterFactory implements FilterFactoryInterface
{
    /**
     * @param stdClass&object{
     *     min_shingle_size?: int,
     *     max_shingle_size?: int,
     *     output_unigrams?: bool,
     *     output_unigrams_if_no_shingles?: bool,
     *     token_separator?: string,
     *     filler_token?: string
     * } $configuration
     */
    public static function create(string $name, stdClass $configuration): ShingleFilter
    {
        return new ShingleFilter(
            $name,
            (int)($configuration->min_shingle_size ?? ShingleFilter::DEFAULT_SHINGLE_SIZE),
            (int)($configuration->max_shingle_size ?? ShingleFilter::DEFAULT_SHINGLE_SIZE),
            (bool)($configuration->output_unigrams ?? true),
            (bool)($configuration->output_unigrams_if_no_shingles ?? false),
            $configuration->token_separator ?? ShingleFilter::DEFAULT_TOKEN_SEPARATOR,
            $configuration->filler_token ?? ShingleFilter::DEFAULT_FILLER_TOKEN
        );
    }
}
