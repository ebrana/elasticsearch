<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore;

use Elasticsearch\Search\Queries\Query;

/**
 * The weight alone - typically together with a filter: "whatever matches this, multiply the score by X".
 */
readonly class WeightFunction extends AbstractScoreFunction
{
    public function __construct(float $weight, ?Query $filter = null)
    {
        parent::__construct($filter, $weight);
    }

    protected function provideFunction(): array
    {
        return [];
    }
}
