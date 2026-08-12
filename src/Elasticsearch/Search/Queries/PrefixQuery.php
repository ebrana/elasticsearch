<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

readonly class PrefixQuery implements Query
{
    public function __construct(
        private string $field,
        private string $value,
        private ?string $rewrite = null,
        private ?bool $case_insensitive = null
    ) {
    }

    public function toArray(): array
    {
        $data = ['value' => $this->value];

        if ($this->rewrite) {
            $data['rewrite'] = $this->rewrite;
        }
        if ($this->case_insensitive) {
            $data['case_insensitive'] = $this->case_insensitive;
        }

        return ['prefix' => [$this->field => $data]];
    }
}
