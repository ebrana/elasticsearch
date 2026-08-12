<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Highlight;

use Elasticsearch\Search\Highlight\Concerns\WithHighlightOptions;

/**
 * Jedno pole, ktere se ma zvyraznit. Bez dalsich voleb se pouziji ty globalni z Highlight.
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
     * Zvyrazni podle shod v jinych polich - typicky varianty stejneho textu
     * (napr. name a name.autocomplete). Funguje jen s fvh highlighterem.
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
