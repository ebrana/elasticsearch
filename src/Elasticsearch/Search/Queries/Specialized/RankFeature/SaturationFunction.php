<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Specialized\RankFeature;

/**
 * Skore roste s hodnotou pole, ale nasyti se - u `pivot` je skore 0.5.
 * Bez pivotu si ho Elasticsearch spocita z dat v indexu.
 */
readonly class SaturationFunction implements RankFeatureFunctionInterface
{
    public function __construct(private ?float $pivot = null)
    {
    }

    public function toArray(): array
    {
        // bez pivotu musi jit do JSONu prazdny objekt, ne prazdne pole -
        // ES jinak hlasi "saturation doesn't support values of type: START_ARRAY"
        return ['saturation' => null === $this->pivot ? (object)[] : ['pivot' => $this->pivot]];
    }
}
