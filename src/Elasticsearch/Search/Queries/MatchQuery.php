<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

readonly class MatchQuery implements Query
{
    use MatchQueryTrait {
        MatchQueryTrait::toArray as traitToArray;
    }

    public function __construct(
        private string $field,
        private string $query,
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

        $match = array_merge($match, iterator_to_array($this->traitToArray()));

        yield 'match' => $match;
    }
}
