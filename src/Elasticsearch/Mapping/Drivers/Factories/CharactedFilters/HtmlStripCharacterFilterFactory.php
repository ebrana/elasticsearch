<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\CharactedFilters;

use Elasticsearch\Mapping\Settings\AbstractCharactedFilter;
use Elasticsearch\Mapping\Settings\CharacterFilters\HtmlStripCharacterFilter;
use stdClass;

class HtmlStripCharacterFilterFactory implements CharacterFilterFactoryInterface
{
    /**
     * @param stdClass&object{escaped_tags?: string[]|null} $configuration
     */
    public static function create(string $name, stdClass $configuration): AbstractCharactedFilter
    {
        return new HtmlStripCharacterFilter($name, $configuration->escaped_tags ?? null);
    }
}
