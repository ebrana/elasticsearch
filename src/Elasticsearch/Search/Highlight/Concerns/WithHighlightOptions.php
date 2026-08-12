<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Highlight\Concerns;

use Elasticsearch\Search\Highlight\Enums\BoundaryScanner;
use Elasticsearch\Search\Highlight\Enums\Fragmenter;
use Elasticsearch\Search\Highlight\Enums\HighlighterType;
use Elasticsearch\Search\Highlight\Enums\HighlightOrder;
use Elasticsearch\Search\Queries\Query;

/**
 * Volby, ktere Elasticsearch pripousti jak globalne, tak u jednotlivych poli.
 * U pole prebiji tu globalni.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/highlighting.html
 */
trait WithHighlightOptions
{
    private ?HighlighterType $type = null;

    /** @var string[]|null */
    private ?array $pre_tags = null;

    /** @var string[]|null */
    private ?array $post_tags = null;

    private ?int $fragment_size = null;
    private ?int $number_of_fragments = null;
    private ?HighlightOrder $order = null;
    private ?bool $require_field_match = null;
    private ?BoundaryScanner $boundary_scanner = null;
    private ?int $boundary_max_scan = null;
    private ?string $boundary_chars = null;
    private ?int $no_match_size = null;
    private ?Query $highlight_query = null;
    private ?int $phrase_limit = null;
    private ?Fragmenter $fragmenter = null;
    private ?int $max_analyzed_offset = null;

    public function setType(?HighlighterType $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Znacky se zadavaji jako pole, protoze Elasticsearch umi obalit kazdou shodu jinak.
     *
     * @param string[]|null $pre_tags
     * @param string[]|null $post_tags
     */
    public function setTags(?array $pre_tags, ?array $post_tags): static
    {
        $this->pre_tags = $pre_tags;
        $this->post_tags = $post_tags;

        return $this;
    }

    public function setFragmentSize(?int $fragment_size): static
    {
        $this->fragment_size = $fragment_size;

        return $this;
    }

    public function setNumberOfFragments(?int $number_of_fragments): static
    {
        $this->number_of_fragments = $number_of_fragments;

        return $this;
    }

    public function setOrder(?HighlightOrder $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function setRequireFieldMatch(?bool $require_field_match): static
    {
        $this->require_field_match = $require_field_match;

        return $this;
    }

    public function setBoundaryScanner(?BoundaryScanner $boundary_scanner): static
    {
        $this->boundary_scanner = $boundary_scanner;

        return $this;
    }

    public function setBoundaryMaxScan(?int $boundary_max_scan): static
    {
        $this->boundary_max_scan = $boundary_max_scan;

        return $this;
    }

    public function setBoundaryChars(?string $boundary_chars): static
    {
        $this->boundary_chars = $boundary_chars;

        return $this;
    }

    /**
     * Kolik znaku vratit, kdyz v poli zadna shoda neni. Bez toho se takove pole vynecha.
     */
    public function setNoMatchSize(?int $no_match_size): static
    {
        $this->no_match_size = $no_match_size;

        return $this;
    }

    /**
     * Zvyrazni podle jine query, nez ktera se hledala.
     */
    public function setHighlightQuery(?Query $highlight_query): static
    {
        $this->highlight_query = $highlight_query;

        return $this;
    }

    public function setPhraseLimit(?int $phrase_limit): static
    {
        $this->phrase_limit = $phrase_limit;

        return $this;
    }

    public function setFragmenter(?Fragmenter $fragmenter): static
    {
        $this->fragmenter = $fragmenter;

        return $this;
    }

    public function setMaxAnalyzedOffset(?int $max_analyzed_offset): static
    {
        $this->max_analyzed_offset = $max_analyzed_offset;

        return $this;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function provideHighlightOptions(array &$data): void
    {
        if (null !== $this->type) {
            $data['type'] = $this->type->value;
        }

        if (null !== $this->pre_tags) {
            $data['pre_tags'] = $this->pre_tags;
        }

        if (null !== $this->post_tags) {
            $data['post_tags'] = $this->post_tags;
        }

        if (null !== $this->fragment_size) {
            $data['fragment_size'] = $this->fragment_size;
        }

        if (null !== $this->number_of_fragments) {
            $data['number_of_fragments'] = $this->number_of_fragments;
        }

        if (null !== $this->order) {
            $data['order'] = $this->order->value;
        }

        if (null !== $this->require_field_match) {
            $data['require_field_match'] = $this->require_field_match;
        }

        if (null !== $this->boundary_scanner) {
            $data['boundary_scanner'] = $this->boundary_scanner->value;
        }

        if (null !== $this->boundary_max_scan) {
            $data['boundary_max_scan'] = $this->boundary_max_scan;
        }

        if (null !== $this->boundary_chars) {
            $data['boundary_chars'] = $this->boundary_chars;
        }

        if (null !== $this->no_match_size) {
            $data['no_match_size'] = $this->no_match_size;
        }

        if (null !== $this->highlight_query) {
            $data['highlight_query'] = $this->highlight_query->toArray();
        }

        if (null !== $this->phrase_limit) {
            $data['phrase_limit'] = $this->phrase_limit;
        }

        if (null !== $this->fragmenter) {
            $data['fragmenter'] = $this->fragmenter->value;
        }

        if (null !== $this->max_analyzed_offset) {
            $data['max_analyzed_offset'] = $this->max_analyzed_offset;
        }
    }
}
