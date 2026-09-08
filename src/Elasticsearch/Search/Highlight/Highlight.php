<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Highlight;

use Elasticsearch\Search\Highlight\Concerns\WithHighlightOptions;
use Elasticsearch\Search\Highlight\Enums\Encoder;
use RuntimeException;

/**
 * The `highlight` section of the request body - it returns fragments of text with the matches
 * highlighted for every hit.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/highlighting.html
 */
final class Highlight
{
    use WithHighlightOptions;

    /** @var HighlightField[] */
    private array $fields = [];

    private ?Encoder $encoder = null;
    private ?string $tags_schema = null;

    public function __construct(HighlightField ...$fields)
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    public function addField(HighlightField $field): self
    {
        $this->fields[$field->getName()] = $field;

        return $this;
    }

    /**
     * @return HighlightField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function setEncoder(?Encoder $encoder): self
    {
        $this->encoder = $encoder;

        return $this;
    }

    /**
     * The only value Elasticsearch knows is "styled" - it uses the prepared tags
     * <em class="hlt1"> through <em class="hlt10">.
     */
    public function useStyledTags(): self
    {
        $this->tags_schema = 'styled';

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if ([] === $this->fields) {
            throw new RuntimeException('Highlight must define at least one field.');
        }

        $data = [];
        $this->provideHighlightOptions($data);

        if (null !== $this->encoder) {
            $data['encoder'] = $this->encoder->value;
        }

        if (null !== $this->tags_schema) {
            $data['tags_schema'] = $this->tags_schema;
        }

        $fields = [];
        foreach ($this->fields as $name => $field) {
            $options = $field->toArray();
            // a field without its own options must be an empty object in the JSON, not an empty array
            $fields[$name] = [] === $options ? (object)[] : $options;
        }
        $data['fields'] = $fields;

        return $data;
    }
}
