<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Settings\Filters\Enums\LowercaseLanguage;
use Elasticsearch\Mapping\Settings\Filters\LowercaseFilter;
use stdClass;

class LowercaseFilterFactory implements FilterFactoryInterface
{
    /**
     * @param stdClass&object{language?: string} $configuration
     */
    public static function create(string $name, stdClass $configuration): LowercaseFilter
    {
        $language = isset($configuration->language)
            ? LowercaseLanguage::tryFrom($configuration->language)
            : null;

        return new LowercaseFilter($name, $language);
    }
}
