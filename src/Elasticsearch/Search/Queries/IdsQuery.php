<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

/**
 * Hleda dokumenty podle _id.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-ids-query.html
 */
readonly class IdsQuery implements Query
{
    /**
     * @param string[] $values
     */
    public function __construct(
        private array $values,
        private ?float $boost = null,
    ) {
    }

    public function toArray(): array
    {
        $data = ['values' => $this->values];

        if (null !== $this->boost) {
            $data['boost'] = $this->boost;
        }

        return ['ids' => $data];
    }
}
