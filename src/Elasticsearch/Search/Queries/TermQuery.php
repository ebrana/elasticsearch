<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

readonly class TermQuery implements Query
{
    /**
     * @param string|bool|int|float|string[] $value
     */
    public function __construct(
        private string $field,
        private string|bool|int|float|array $value,
        private ?float $boost = null,
    ) {
    }

    public function toArray(): Generator
    {
        $data = [
            $this->field => $this->value,
        ];

        if ($this->boost) {
            $data['boost'] = $this->boost;
        }

        yield 'term' => $data;
    }
}
