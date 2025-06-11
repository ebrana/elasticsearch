<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Settings\Filters\HunspellFilter;
use stdClass;

class HunspellFilterFactory implements FilterFactoryInterface
{
    /**
     * @param stdClass&object{locale?: string, dictionary?: string, dedup?: bool, longest_only?: bool} $configuration
     */
    public static function create(string $name, stdClass $configuration): HunspellFilter
    {
        $locale = 'en_US';
        $dictionary = 'english';
        $dedup = false;
        $longest_only = false;

        if (isset($configuration->locale)) {
            $locale = $configuration->locale;
        }
        if (isset($configuration->dictionary)) {
            $dictionary = $configuration->dictionary;
        }
        if (isset($configuration->dedup)) {
            $dedup = $configuration->dedup;
        }
        if (isset($configuration->dictionary)) {
            $dictionary = $configuration->dictionary;
        }
        if (isset($configuration->longest_only)) {
            $longest_only = $configuration->longest_only;
        }

        return new HunspellFilter(
            name:$name,
            locale: $locale,
            dictionary: $dictionary,
            dedup: $dedup,
            longest_only: $longest_only
        );
    }
}
