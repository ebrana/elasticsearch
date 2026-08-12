<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Results;

/**
 * Jeden vstupni usek textu a navrhy k nemu. Term suggester vraci jednu polozku
 * za kazde slovo vstupu, phrase a completion jednu za cely vstup.
 */
final readonly class SuggestEntry
{
    /**
     * @param SuggestOption[] $options
     */
    public function __construct(
        private string $text,
        private int $offset,
        private int $length,
        private array $options,
    ) {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @return SuggestOption[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Jen texty navrhu - typicky to, co se posila do frontendu.
     *
     * @return string[]
     */
    public function getOptionTexts(): array
    {
        return array_map(static fn (SuggestOption $option): string => $option->getText(), $this->options);
    }

    public function getFirstOption(): ?SuggestOption
    {
        return $this->options[0] ?? null;
    }
}
