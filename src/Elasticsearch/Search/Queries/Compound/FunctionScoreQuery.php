<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound;

use Elasticsearch\Search\Queries\Compound\FunctionScore\Enums\BoostMode;
use Elasticsearch\Search\Queries\Compound\FunctionScore\Enums\ScoreMode;
use Elasticsearch\Search\Queries\Compound\FunctionScore\ScoreFunctionInterface;
use Elasticsearch\Search\Queries\Query;

/**
 * Prepocita skore shod pomoci jedne nebo vice funkci. `score_mode` rika, jak se slozi
 * funkce mezi sebou, `boost_mode` jak se vysledek spoji se skorem z puvodni query.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html
 */
class FunctionScoreQuery implements Query
{
    /** @var ScoreFunctionInterface[] */
    private array $functions;

    /**
     * @param ScoreFunctionInterface[] $functions
     */
    public function __construct(
        private readonly Query $query,
        array $functions = [],
        private readonly ?ScoreMode $score_mode = null,
        private readonly ?BoostMode $boost_mode = null,
        private readonly ?float $max_boost = null,
        private readonly ?float $min_score = null,
        private readonly ?float $boost = null,
    ) {
        $this->functions = $functions;
    }

    public function addFunction(ScoreFunctionInterface $function): self
    {
        $this->functions[] = $function;

        return $this;
    }

    /**
     * @return ScoreFunctionInterface[]
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }

    public function toArray(): array
    {
        $data = ['query' => $this->query->toArray()];

        if ($this->functions) {
            $data['functions'] = array_map(
                static fn (ScoreFunctionInterface $function): array => $function->toArray(),
                $this->functions
            );
        }

        if (null !== $this->score_mode) {
            $data['score_mode'] = $this->score_mode->value;
        }

        if (null !== $this->boost_mode) {
            $data['boost_mode'] = $this->boost_mode->value;
        }

        if (null !== $this->max_boost) {
            $data['max_boost'] = $this->max_boost;
        }

        if (null !== $this->min_score) {
            $data['min_score'] = $this->min_score;
        }

        if (null !== $this->boost) {
            $data['boost'] = $this->boost;
        }

        return ['function_score' => $data];
    }
}
