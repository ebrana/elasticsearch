<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

readonly class TermsQuery implements Query
{
    /**
     * @param string   $field
     * @param string[] $value
     */
    public function __construct(
        private string $field,
        private array $value
    ) {
    }

    public function toArray(): Generator
    {
        yield 'terms' => [
            $this->field => $this->value,
        ];
    }
}
