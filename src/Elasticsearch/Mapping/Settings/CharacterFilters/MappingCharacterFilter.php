<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\CharacterFilters;

use Attribute;
use Elasticsearch\Mapping\Exceptions\AttributeMissingException;
use Elasticsearch\Mapping\Settings\AbstractCharactedFilter;

#[Attribute(Attribute::TARGET_CLASS)]
class MappingCharacterFilter extends AbstractCharactedFilter
{
    /**
     * @param array<string, string>|null  $mappings
     */
    public function __construct(
        string $name,
        private ?array $mappings = null,
        private ?string $mappings_path = null
    ) {
        parent::__construct($name, 'mapping');
    }

    public function addMapping(string $key, string $value): void
    {
        if (null === $this->mappings) {
            $this->mappings = [];
        }
        $this->mappings[$key] = $value;
    }

    public function setMappingsPath(string $mappings_path): void
    {
        $this->mappings_path = $mappings_path;
    }

    /**
     * @throws \Elasticsearch\Mapping\Exceptions\AttributeMissingException
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (null === $this->mappings && null === $this->mappings_path) {
            throw new AttributeMissingException('Mapping Character Filter must define mappings or mappings_path.');
        }

        if ($this->mappings_path) {
            $data['mappings_path'] = $this->mappings_path;
        } elseif ($this->mappings) {
            $data['mappings'] = $this->mappings;
        }

        return $data;
    }
}
