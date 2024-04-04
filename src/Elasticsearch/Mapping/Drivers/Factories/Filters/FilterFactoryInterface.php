<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Settings\AbstractFilter;
use stdClass;

interface FilterFactoryInterface
{
    public static function create(string $name, stdClass $configuration): AbstractFilter;
}
