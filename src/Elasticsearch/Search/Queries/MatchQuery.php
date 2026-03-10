<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

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

    public function toArray(): array
    {
        $match = [
            $this->field => [
                'query' => $this->query,
            ],
        ];

        if ($this->fuzziness) {
            $match[$this->field]['fuzziness'] = $this->fuzziness;
        }

        $match[$this->field] = array_merge($match[$this->field], $this->traitToArray());

        return ['match' => $match];
    }
}
