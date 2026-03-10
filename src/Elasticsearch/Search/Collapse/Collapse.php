<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Collapse;

/**
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-collapse.html
 */
readonly class Collapse
{
    public function __construct(
        private string $field,
        private InnerHitsCollection $innerHits,
        private ?int $max_concurrent_group_searches = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [
            'field' => $this->field,
            'inner_hits' => $this->innerHits->toArray(),
        ];

        if ($this->max_concurrent_group_searches) {
            $result['max_concurrent_group_searches'] = $this->max_concurrent_group_searches;
        }

        return $result;
    }
}
