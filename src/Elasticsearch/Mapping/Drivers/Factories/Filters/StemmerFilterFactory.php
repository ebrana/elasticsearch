<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Settings\Filters\Enums\Language;
use Elasticsearch\Mapping\Settings\Filters\StemmerFilter;
use stdClass;

class StemmerFilterFactory implements FilterFactoryInterface
{
    /**
     * @param stdClass&object{language?: string} $configuration
     */
    public static function create(string $name, stdClass $configuration): StemmerFilter
    {
        $language = Language::ENGLISH;
        if (isset($configuration->language)) {
            $language = Language::from($configuration->language);
        }

        return new StemmerFilter($name, $language);
    }
}
