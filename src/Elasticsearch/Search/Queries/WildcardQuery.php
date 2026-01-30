<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

readonly class WildcardQuery implements Query
{
    public function __construct(
        private string $field,
        private string $value,
        private ?float $boost = null,
        private ?string $rewrite = null,
        private ?bool $case_insensitive = null
    ) {
    }

    public function toArray(): Generator
    {
        $data = [
            $this->field => [
                'value' => $this->value,
            ]
        ];
        if ($this->boost) {
            $data['boost'] = $this->boost;
        }
        if ($this->rewrite) {
            $data['rewrite'] = $this->rewrite;
        }
        if ($this->case_insensitive) {
            $data['case_insensitive'] = $this->case_insensitive;
        }

        yield 'wildcard' => $data;
    }
}
