<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings;

use Attribute;

/**
 * Normalizer je obdoba analyzeru pro keyword pole - nema tokenizer a vysledkem je vzdy
 * jediny token. Pouziva se na sjednoceni hodnot pro razeni a fasety (napr. lowercase
 * + asciifolding). Na pole se zapoji pres KeywordType(normalizer: "...").
 *
 * Elasticsearch u normalizeru povoluje jen cast filtru (ty, ktere nemeni pocet tokenu).
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
