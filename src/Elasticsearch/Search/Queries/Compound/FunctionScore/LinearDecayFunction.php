<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore;

use Elasticsearch\Search\Queries\Compound\FunctionScore\Enums\MultiValueMode;
use Elasticsearch\Search\Queries\Query;

/**
 * A linear decay. Unlike gauss and exp it reaches zero - documents farther than twice the scale
 * get a score of 0.
 *
 * It is named LinearDecayFunction so that it is not confused with RankFeature\LinearFunction;
 * in the JSON it is the `linear` key in both cases.
 */
readonly class LinearDecayFunction extends AbstractDecayFunction
{
    /**
     * @param string|int|float|array<int|string, mixed> $origin
     */
    public function __construct(
        string $field,
        string|int|float|array $origin,
        string|int|float $scale,
        string|int|float|null $offset = null,
        ?float $decay = null,
        ?MultiValueMode $multi_value_mode = null,
        ?Query $filter = null,
        ?float $weight = null,
    ) {
        parent::__construct(
            'linear',
            $field,
            $origin,
            $scale,
            $offset,
            $decay,
            $multi_value_mode,
            $filter,
            $weight
        );
    }
}
