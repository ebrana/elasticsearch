<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Analyzer
{
    /**
     * @param string[] $filters
     */
    public function __construct(
        private string $name,
        private string $tokenizer,
        private array $filters
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
     * @return array<string, string|string[]>
     */
    public function toArray(): array
    {
        return [
            'type'      => $this->getType(),
            'tokenizer' => $this->getTokenizer(),
            'filter'    => $this->getFilters(),
        ];
    }
}
