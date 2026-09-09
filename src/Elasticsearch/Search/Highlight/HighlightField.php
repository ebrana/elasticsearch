<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Highlight;

use Elasticsearch\Search\Highlight\Concerns\WithHighlightOptions;

/**
 * A single field to be highlighted. Without further options the global ones from Highlight are used.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/highlighting.html
 */
final class HighlightField
{
    use WithHighlightOptions;

    /** @var string[]|null */
    private ?array $matched_fields = null;

    private ?int $fragment_offset = null;

    public function __construct(private readonly string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Highlights by the matches in other fields - typically variants of the same text
     * (e.g. name and name.autocomplete). Works only with the fvh highlighter.
     *
     * @param string[]|null $matched_fields
     */
    public function setMatchedFields(?array $matched_fields): self
    {
        $this->matched_fields = $matched_fields;

        return $this;
    }

    public function setFragmentOffset(?int $fragment_offset): self
    {
        $this->fragment_offset = $fragment_offset;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];
        $this->provideHighlightOptions($data);

        if (null !== $this->matched_fields) {
            $data['matched_fields'] = $this->matched_fields;
        }

        if (null !== $this->fragment_offset) {
            $data['fragment_offset'] = $this->fragment_offset;
        }

        return $data;
    }
}
