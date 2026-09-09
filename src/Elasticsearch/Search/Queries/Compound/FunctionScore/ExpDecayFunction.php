<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore;

use Elasticsearch\Search\Queries\Compound\FunctionScore\Enums\MultiValueMode;
use Elasticsearch\Search\Queries\Query;

/**
 * Exponencialni pokles - nejrychlejsi hned u originu.
 */
readonly class ExpDecayFunction extends AbstractDecayFunction
{
    /**
     * @param string|int|float|array<int|string, mixed> $origin
     * @param string|int|float $scale
     * @param string|int|float|null $offset
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
            'exp',
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
