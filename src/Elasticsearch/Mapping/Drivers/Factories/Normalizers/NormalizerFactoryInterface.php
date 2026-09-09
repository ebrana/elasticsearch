<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Normalizers;

use Elasticsearch\Mapping\Settings\Normalizer;
use stdClass;

interface NormalizerFactoryInterface
{
    public static function create(string $name, stdClass $configuration): Normalizer;
}
