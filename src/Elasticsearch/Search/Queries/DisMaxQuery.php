<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

class DisMaxQuery implements Query
{
    /**
     * @param Query[] $queries
     */
    public function __construct(
        private array $queries = [],
        private ?float $tie_breaker = null,
    ) {
    }

    /**
     * @return Query[]
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    public function addQuery(Query $query): void
    {
        $this->queries[] = $query;
    }

    public function getTieBreaker(): ?float
    {
        return $this->tie_breaker;
    }

    public function setTieBreaker(?float $tie_breaker): void
    {
        $this->tie_breaker = $tie_breaker;
    }

    public function toArray(): Generator
    {
        $body = [
            'queries' => [],
        ];

        foreach ($this->queries as $query) {
            $body['queries'][] = iterator_to_array($query->toArray());
        }

        if ($this->tie_breaker) {
            $body['tie_breaker'] = $this->tie_breaker;
        }

        yield 'dis_max' => $body;
    }
}
