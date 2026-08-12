<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Specialized\RankFeature;

/**
 * Skore = log(scaling_factor + hodnota pole). Roste neomezene, ale se zpomalujici tendenci.
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
