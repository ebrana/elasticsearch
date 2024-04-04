<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Settings\Filters\EdgeNgramAbstractFilter;
use Elasticsearch\Mapping\Settings\Filters\Enums\Side;
use stdClass;

class EdgeNgramFilterFactory implements FilterFactoryInterface
{
    public static function create(string $name, stdClass $configuration): EdgeNgramAbstractFilter
    {
        $min_gram = 1;
        $max_gram = 2;
        $preserve_original = false;
        $side = Side::FRONT;

        if (isset($configuration->min_gram)) {
            $min_gram = (int)$configuration->min_gram;
        }

        if (isset($configuration->max_gram)) {
            $max_gram = (int)$configuration->max_gram;
        }

        if (isset($configuration->preserve_original)) {
            $preserve_original = (bool)$configuration->preserve_original;
        }

        if (isset($configuration->side)) {
            $side = match ($configuration->side) {
                'back' => Side::BACK,
                default => Side::FRONT
            };
        }

        return new EdgeNgramAbstractFilter($name, $min_gram, $max_gram, $preserve_original, $side);
    }
}
