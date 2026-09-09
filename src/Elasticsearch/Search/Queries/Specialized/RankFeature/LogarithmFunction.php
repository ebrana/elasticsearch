<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Specialized\RankFeature;

/**
 * Score = log(scaling_factor + field value). It grows without a bound, but with a slowing tendency.
 */
readonly class LogarithmFunction implements RankFeatureFunctionInterface
{
    public function __construct(private float $scaling_factor)
    {
    }

    public function toArray(): array
    {
        return ['log' => ['scaling_factor' => $this->scaling_factor]];
    }
}
