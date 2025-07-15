<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Elasticsearch\Search\Queries\Enums\MultiMatchType;
use Generator;

class MultiMatchQuery implements Query
{
    use MatchQueryTrait {
        MatchQueryTrait::toArray as traitToArray;
    }

    /**
     * @param string[] $fields
     */
    public function __construct(
        private readonly string $query,
        private readonly array $fields,
        private readonly ?MultiMatchType $type = null,
        private readonly ?float $tie_breaker = null,
    ) {
    }

    public function toArray(): Generator
    {
        $multiMatch = [
            'query'  => $this->query,
            'fields' => $this->fields,
        ];

        if ($this->type) {
            $multiMatch['type'] = $this->type->value;
        }

        if ($this->tie_breaker) {
            $multiMatch['tie_breaker'] = $this->tie_breaker;
        }

        if (MultiMatchType::BEST_FIELDS === $this->type) {
            $multiMatch = array_merge($multiMatch, iterator_to_array($this->traitToArray()));
        }

        yield 'multi_match' => $multiMatch;
    }
}
