<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Settings\Filters\NgramAbstractFilter;
use stdClass;

class NgramFilterFactory implements FilterFactoryInterface
{
    public static function create(string $name, stdClass $configuration): NgramAbstractFilter
    {
        $min_gram = 1;
        $max_gram = 2;
        $preserve_original = false;

        if (isset($configuration->min_gram)) {
            $min_gram = (int)$configuration->min_gram;
        }

        if (isset($configuration->max_gram)) {
            $max_gram = (int)$configuration->max_gram;
        }

        if (isset($configuration->preserve_original)) {
            $preserve_original = (bool)$configuration->preserve_original;
        }

        return new NgramAbstractFilter($name, $min_gram, $max_gram, $preserve_original);
    }
}
