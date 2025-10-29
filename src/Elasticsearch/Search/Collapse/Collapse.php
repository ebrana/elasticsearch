<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Collapse;

use Generator;

readonly class Collapse
{
    public function __construct(
        private string $field,
        private InnerHitsCollection $innerHits,
        private ?int $max_concurrent_group_searches = null,
    ) {}

    public function toArray(): Generator
    {
        $result = [
            'field' => $this->field,
            'inner_hits' => iterator_to_array($this->innerHits->toArray()),
        ];

        if ($this->max_concurrent_group_searches) {
            $result['max_concurrent_group_searches'] = $this->max_concurrent_group_searches;
        }

        yield $result;
    }
}
