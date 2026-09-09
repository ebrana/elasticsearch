<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Specialized;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Specialized\RankFeatureType;
use stdClass;

class RankFeatureTypeFactory implements PropertyFactoryInterface
{
    public static function create(string $name, stdClass $configuration): AbstractType
    {
        return new RankFeatureType(
            (bool)($configuration->positive_score_impact ?? true),
            $name
        );
    }
}
