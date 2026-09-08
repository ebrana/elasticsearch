<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore;

use Elasticsearch\Search\Queries\Query;

/**
 * A score from a script. The script is passed the way Elasticsearch expects it, i.e. including
 * the `source` key and an optional `params`.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html#function-script-score
 */
readonly class ScriptScoreFunction extends AbstractScoreFunction
{
    /**
     * @param array<string, mixed> $script
     */
    public function __construct(
        private array $script,
        ?Query $filter = null,
        ?float $weight = null,
    ) {
        parent::__construct($filter, $weight);
    }

    protected function provideFunction(): array
    {
        return ['script_score' => ['script' => $this->script]];
    }
}
