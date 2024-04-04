<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\CharactedFilters;

use Elasticsearch\Mapping\Exceptions\AttributeMissingException;
use Elasticsearch\Mapping\Settings\AbstractCharactedFilter;
use Elasticsearch\Mapping\Settings\CharacterFilters\MappingCharacterFilter;
use stdClass;

class MappingCharacterFilterFactory implements CharacterFilterFactoryInterface
{
    /**
     * @throws \Elasticsearch\Mapping\Exceptions\AttributeMissingException
     */
    public static function create(string $name, stdClass $configuration): AbstractCharactedFilter
    {
        if (!isset($configuration->mappings) && !isset($configuration->mappings_path)) {
            throw new AttributeMissingException('Mapping Character Filter must define mappings or mappings_path.');
        }

        $mappings = $mappings_path = null;

        if (isset($configuration->mappings)) {
            $mappings = $configuration->mappings;
        } else {
            $mappings_path = $configuration->mappings_path;
        }

        return new MappingCharacterFilter($name, $mappings, $mappings_path);
    }
}
