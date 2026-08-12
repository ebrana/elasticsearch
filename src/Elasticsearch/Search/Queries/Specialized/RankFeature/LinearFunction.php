<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Specialized\RankFeature;

/**
 * Skore je primo proporcni hodnote pole. Nema zadne parametry.
 */
readonly class LinearFunction implements RankFeatureFunctionInterface
{
    public function toArray(): array
    {
        // prazdny objekt, ne prazdne pole - ES pole odmita
        return ['linear' => (object)[]];
    }
}
