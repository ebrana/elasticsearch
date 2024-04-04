<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\CharacterFilters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractCharactedFilter;
use Elasticsearch\Mapping\Settings\CharacterFilters\Enums\Flags;

#[Attribute(Attribute::TARGET_CLASS)]
class PatternReplaceCharacterFilter extends AbstractCharactedFilter
{
    /** @var Flags[] */
    private ?array $flags = null;

    public function __construct(
        string $name,
        private readonly string $pattern,
        private readonly string $replacement
    ) {
        parent::__construct($name, 'pattern_replace');
    }

    public function addFlag(Flags $flag): static
    {
        $this->flags[$flag->value] = $flag;

        return $this;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['pattern'] = $this->pattern;
        $data['replacement'] = $this->replacement;

        if ($this->flags) {
            $data['flags'] = implode('|', array_keys($this->flags));
        }

        return $data;
    }
}
