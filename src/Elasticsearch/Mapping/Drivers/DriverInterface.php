<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers;

use Elasticsearch\Mapping\Index;

interface DriverInterface
{
    public function loadMetadata(string $class): Index;
}
