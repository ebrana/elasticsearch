<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties;

use Elasticsearch\Mapping\Types\AbstractType;
use stdClass;

interface PropertyFactoryInterface
{
    public static function create(string $name, stdClass $configuration): AbstractType;
}
