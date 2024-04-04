<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\CharacterFilters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractCharactedFilter;

#[Attribute(Attribute::TARGET_CLASS)]
class HtmlStripCharacterFilter extends AbstractCharactedFilter
{
    /**
     * @param string[]|null $escaped_tags
     */
    public function __construct(
        string $name,
        private ?array $escaped_tags = null
    ) {
        parent::__construct($name, 'html_strip');
    }

    public function addEscapedTag(string $value): void
    {
        if (null === $this->escaped_tags) {
            $this->escaped_tags = [];
        }
        $this->escaped_tags[] = $value;
    }

    public function toArray(): array
    {
        $data = parent::toArray();

        if ($this->escaped_tags) {
            $data['escaped_tags'] = $this->escaped_tags;
        }

        return $data;
    }
}
