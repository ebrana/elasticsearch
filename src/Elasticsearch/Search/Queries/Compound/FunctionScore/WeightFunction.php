<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore;

use Elasticsearch\Search\Queries\Query;

/**
 * Samotna vaha - typicky ve spojeni s filtrem: "co odpovida tomuhle, nasob skore X".
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
