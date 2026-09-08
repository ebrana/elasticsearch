<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Elasticsearch\Search\Queries\Enums\RegexpFlag;

/**
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html
 */
readonly class RegexpQuery implements Query
{
    /**
     * @param RegexpFlag[] $flags
     */
    public function __construct(
        private string $field,
        private string $value,
        private array $flags = [],
        private ?bool $case_insensitive = null,
        private ?int $max_determinized_states = null,
        private ?string $rewrite = null,
        private ?float $boost = null,
    ) {
    }

    public function toArray(): array
    {
        $data = ['value' => $this->value];

        if ($this->flags) {
            $data['flags'] = implode(
                '|',
                array_map(static fn (RegexpFlag $flag): string => $flag->value, $this->flags)
            );
        }

        if (null !== $this->case_insensitive) {
            $data['case_insensitive'] = $this->case_insensitive;
        }

        if (null !== $this->max_determinized_states) {
            $data['max_determinized_states'] = $this->max_determinized_states;
        }

        if (null !== $this->rewrite) {
            $data['rewrite'] = $this->rewrite;
        }

        if (null !== $this->boost) {
            $data['boost'] = $this->boost;
        }

        return ['regexp' => [$this->field => $data]];
    }
}
