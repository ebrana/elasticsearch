<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Specialized\RankFeature;

/**
 * The score grows with the field value, but it saturates - at `pivot` the score is 0.5.
 * Without a pivot Elasticsearch computes one from the data in the index.
 */
readonly class SaturationFunction implements RankFeatureFunctionInterface
{
    public function __construct(private ?float $pivot = null)
    {
    }

    public function toArray(): array
    {
        // without a pivot an empty object must go into the JSON, not an empty array -
        // otherwise ES reports "saturation doesn't support values of type: START_ARRAY"
        return ['saturation' => null === $this->pivot ? (object)[] : ['pivot' => $this->pivot]];
    }
}
