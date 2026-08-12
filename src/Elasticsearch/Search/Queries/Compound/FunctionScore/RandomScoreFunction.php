<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore;

use Elasticsearch\Search\Queries\Query;

/**
 * Nahodne skore. Se `seed` je vysledek pro stejny seed stabilni, coz se hodi na michani
 * vypisu po sezenich; Elasticsearch pak doporucuje zadat i `field` (napr. "_seq_no").
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

        // bez parametru musi jit do JSONu prazdny objekt, ne prazdne pole
        return ['random_score' => [] === $function ? (object)[] : $function];
    }
}
