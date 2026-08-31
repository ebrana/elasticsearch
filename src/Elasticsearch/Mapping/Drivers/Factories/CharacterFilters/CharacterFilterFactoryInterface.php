<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\CharacterFilters;

use Elasticsearch\Mapping\Settings\AbstractCharacterFilter;
use stdClass;

interface CharacterFilterFactoryInterface
{
    public static function create(string $name, stdClass $configuration): AbstractCharacterFilter;
}
