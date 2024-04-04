<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\CharactedFilters;

use Elasticsearch\Mapping\Settings\AbstractCharactedFilter;
use stdClass;

interface CharacterFilterFactoryInterface
{
    public static function create(string $name, stdClass $configuration): AbstractCharactedFilter;
}
