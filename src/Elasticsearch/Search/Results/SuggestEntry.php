<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Results;

/**
 * A single input fragment of text and the suggestions for it. The term suggester returns one entry
 * per input word, phrase and completion one per the whole input.
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
     * Only the suggestion texts - typically what gets sent to the frontend.
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
