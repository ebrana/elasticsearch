<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore;

use Elasticsearch\Search\Queries\Compound\FunctionScore\Enums\MultiValueMode;
use Elasticsearch\Search\Queries\Query;

/**
 * Linearni pokles. Na rozdil od gauss a exp dojde na nule - dokumenty dal nez
 * dvojnasobek scale dostanou skore 0.
 *
 * Jmenuje se LinearDecayFunction, aby se nemichala s RankFeature\LinearFunction;
 * v JSONu je to klic `linear` v obou pripadech.
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
