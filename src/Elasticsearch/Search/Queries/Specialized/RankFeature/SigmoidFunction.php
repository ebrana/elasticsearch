<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Specialized\RankFeature;

/**
 * Jako saturation, ale `exponent` navic ridi, jak strme se krivka u pivotu lame.
 */
readonly class SigmoidFunction implements RankFeatureFunctionInterface
{
    public function __construct(
        private float $pivot,
        private float $exponent,
    ) {
    }

    public function toArray(): array
    {
        return ['sigmoid' => ['pivot' => $this->pivot, 'exponent' => $this->exponent]];
    }
}
