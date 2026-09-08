<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings;

use Attribute;

/**
 * A normalizer is the keyword-field counterpart of an analyzer - it has no tokenizer and always
 * produces a single token. It is used to unify values for sorting and facets (e.g. lowercase
 * + asciifolding). It is attached to a field via KeywordType(normalizer: "...").
 *
 * Elasticsearch allows only a subset of filters in a normalizer (those that do not change the token count).
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-normalizers.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
final readonly class Normalizer
{
    /**
     * @param string[] $filters
     * @param string[] $charFilters
     */
    public function __construct(
        private string $name,
        private array $filters = [],
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
            'type' => $this->getType(),
        ];

        if ($this->filters) {
            $data['filter'] = $this->getFilters();
        }

        if ($this->charFilters) {
            $data['char_filter'] = $this->getCharFilters();
        }

        return $data;
    }
}
