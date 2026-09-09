<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Results;

/**
 * A single suggestion from a suggester response. `freq` is returned by the term suggester only,
 * `_id`/`_index`/`_source` by the completion suggester only.
 */
final readonly class SuggestOption
{
    /**
     * @param array<string, mixed>|null $source
     */
    public function __construct(
        private string $text,
        private ?float $score = null,
        private ?int $freq = null,
        private ?string $id = null,
        private ?string $index = null,
        private ?array $source = null,
    ) {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function getFreq(): ?int
    {
        return $this->freq;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getIndex(): ?string
    {
        return $this->index;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getSource(): ?array
    {
        return $this->source;
    }
}
