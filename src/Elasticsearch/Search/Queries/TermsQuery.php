<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

readonly class TermsQuery implements Query
{
    /**
     * @param string[] $value
     */
    public function __construct(
        private string $field,
        private array $value,
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

        yield 'terms' => $data;
    }
}
