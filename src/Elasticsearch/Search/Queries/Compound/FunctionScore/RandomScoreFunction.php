<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore;

use Elasticsearch\Search\Queries\Query;

/**
 * A random score. With a `seed` the result is stable for the same seed, which is useful for
 * shuffling listings per session; Elasticsearch then recommends giving `field` as well (e.g. "_seq_no").
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html#function-random
 */
readonly class RandomScoreFunction extends AbstractScoreFunction
{
    public function __construct(
        private ?int $seed = null,
        private ?string $field = null,
        ?Query $filter = null,
        ?float $weight = null,
    ) {
        parent::__construct($filter, $weight);
    }

    protected function provideFunction(): array
    {
        $function = [];

        if (null !== $this->seed) {
            $function['seed'] = $this->seed;
        }

        if (null !== $this->field) {
            $function['field'] = $this->field;
        }

        // with no parameters an empty object must go into the JSON, not an empty array
        return ['random_score' => [] === $function ? (object)[] : $function];
    }
}
