<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Specialized\RankFeature;

/**
 * The score is directly proportional to the field value. It takes no parameters.
 */
readonly class LinearFunction implements RankFeatureFunctionInterface
{
    public function toArray(): array
    {
        // an empty object, not an empty array - ES rejects an array
        return ['linear' => (object)[]];
    }
}
