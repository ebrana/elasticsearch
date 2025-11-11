<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

class MatchQuery implements Query
{
    use MatchQueryTrait {
        MatchQueryTrait::toArray as traitToArray;
    }

    public function __construct(
        private readonly string $field,
        private readonly string $query,
    ) {
    }

    public function toArray(): Generator
    {
        $match = [
            $this->field => [
                'query' => $this->query,
            ],
        ];

        if ($this->fuzziness) {
            $match[$this->field]['fuzziness'] = $this->fuzziness;
        }

        $match[$this->field] = array_merge($match[$this->field], ...iterator_to_array($this->traitToArray()));

        yield 'match' => $match;
    }
}
