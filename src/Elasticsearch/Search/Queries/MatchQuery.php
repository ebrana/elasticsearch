<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

readonly class MatchQuery implements Query
{
    public function __construct(
        private string $field,
        private string $query,
        private ?int $fuzziness = null
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

        yield 'match' => $match;
    }
}
