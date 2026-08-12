<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
final readonly class Analyzer
{
    /**
     * @param string[] $filters
     * @param string[] $charFilters
     */
    public function __construct(
        private string $name,
        private string $tokenizer,
        private array $filters,
        private array $charFilters = []
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return 'custom';
    }

    public function getTokenizer(): string
    {
        return $this->tokenizer;
    }

    /**
     * @return string[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return string[]
     */
    public function getCharFilters(): array
    {
        return $this->charFilters;
    }

    /**
     * @return array<string, string|string[]>
     */
    public function toArray(): array
    {
        $data = [
            'type'      => $this->getType(),
            'tokenizer' => $this->getTokenizer(),
            'filter'    => $this->getFilters(),
        ];

        if ($this->charFilters) {
            $data['char_filter'] = $this->getCharFilters();
        }

        return $data;
    }
}
