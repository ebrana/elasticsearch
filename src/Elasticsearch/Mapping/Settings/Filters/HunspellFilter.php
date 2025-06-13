<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractFilter;

#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class HunspellFilter extends AbstractFilter
{
    public function __construct(
        string $name,
        private string $locale,
        private ?string $dictionary = null,
        private bool $dedup = false,
        private bool $longest_only = false,
    ) {
        parent::__construct($name, 'hunspell');
    }

    public function getDictionary(): ?string
    {
        return $this->dictionary;
    }

    public function setDictionary(string $dictionary): void
    {
        $this->dictionary = $dictionary;
    }

    public function isDedup(): bool
    {
        return $this->dedup;
    }

    public function setDedup(bool $dedup): void
    {
        $this->dedup = $dedup;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function isLongestOnly(): bool
    {
        return $this->longest_only;
    }

    public function setLongestOnly(bool $longest_only): void
    {
        $this->longest_only = $longest_only;
    }

    /**
     * @return array<string, array<string>|string|true>
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['locale'] = $this->getLocale();

        if ($this->getDictionary()) {
            $data['dictionary'] = $this->getDictionary();
        }

        if ($this->isDedup()) {
            $data['dedup'] = true;
        }

        if ($this->isLongestOnly()) {
            $data['longest_only'] = true;
        }

        return $data;
    }
}
