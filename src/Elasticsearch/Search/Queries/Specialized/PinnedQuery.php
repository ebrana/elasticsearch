<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Specialized;

use Elasticsearch\Search\Queries\Query;
use RuntimeException;

/**
 * Vybrane dokumenty vytahne na zacatek vysledku, zbytek dohleda `organic` query.
 * Typicke pro placene pozice nebo rucne kurirovane vysledky. Zada se bud `ids`,
 * nebo `docs` (kdyz je potreba i index), ne obojí.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-pinned-query.html
 */
readonly class PinnedQuery implements Query
{
    /**
     * @param string[] $ids
     * @param array<int, array{_id: string, _index?: string}> $docs
     */
    public function __construct(
        private Query $organic,
        private array $ids = [],
        private array $docs = [],
    ) {
    }

    public function toArray(): array
    {
        if ($this->ids && $this->docs) {
            throw new RuntimeException('Pinned query accepts either ids or docs, not both.');
        }

        if (!$this->ids && !$this->docs) {
            throw new RuntimeException('Pinned query must define ids or docs.');
        }

        $data = ['organic' => $this->organic->toArray()];

        if ($this->ids) {
            $data['ids'] = $this->ids;
        } else {
            $data['docs'] = $this->docs;
        }

        return ['pinned' => $data];
    }
}
