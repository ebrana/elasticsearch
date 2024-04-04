<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Tokenizers;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractTokenizer;

#[Attribute(Attribute::TARGET_CLASS)]
class PatternTokenizer extends AbstractTokenizer
{
    public function __construct(
        string $name,
        private string $pattern = '\W+',
        private ?string $flags = null,
        private int $group = -1
    ) {
        parent::__construct($name, 'pattern');
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    public function getFlags(): ?string
    {
        return $this->flags;
    }

    public function setFlags(?string $flags): void
    {
        $this->flags = $flags;
    }

    public function getGroup(): int
    {
        return $this->group;
    }

    public function setGroup(int $group): void
    {
        $this->group = $group;
    }

    /**
     * @return array<string, array<string>|int|string|null>
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['pattern'] = $this->getPattern();

        if ($this->flags) {
            $data['flags'] = $this->getFlags();
        }

        if ($this->group !== -1) {
            $data['group'] = $this->getGroup();
        }

        return $data;
    }
}
