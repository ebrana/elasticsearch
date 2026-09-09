<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractFilter;

/**
 * Token filtr - na rozdil od PatternReplaceCharacterFilter pracuje az na hotovych tokenech.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-pattern-replace-tokenfilter.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class PatternReplaceFilter extends AbstractFilter
{
    public function __construct(
        string $name,
        private string $pattern,
        private string $replacement = '',
        private bool $all = true,
    ) {
        parent::__construct($name, 'pattern_replace');
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    public function getReplacement(): string
    {
        return $this->replacement;
    }

    public function setReplacement(string $replacement): void
    {
        $this->replacement = $replacement;
    }

    public function isAll(): bool
    {
        return $this->all;
    }

    public function setAll(bool $all): void
    {
        $this->all = $all;
    }

    /**
     * @return array<string, array<string>|bool|string>
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['pattern'] = $this->pattern;
        $data['replacement'] = $this->replacement;

        if (false === $this->all) {
            $data['all'] = false;
        }

        return $data;
    }
}
