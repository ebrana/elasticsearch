<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Settings\Filters\AsciiFoldingFilter;
use stdClass;

class AsciiFoldingFilterFactory implements FilterFactoryInterface
{
    /**
     * @param stdClass&object{preserve_original?: bool} $configuration
     */
    public static function create(string $name, stdClass $configuration): AsciiFoldingFilter
    {
        return new AsciiFoldingFilter($name, (bool)($configuration->preserve_original ?? false));
    }
}
