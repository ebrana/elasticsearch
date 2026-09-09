<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties;

use Elasticsearch\Mapping\Exceptions\AttributeMissingException;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\AliasType;
use stdClass;

class AliasTypeFactory implements PropertyFactoryInterface
{
    /**
     * Reads the required path - the field the alias points to.
     *
     * @throws \Elasticsearch\Mapping\Exceptions\AttributeMissingException
     */
    public static function create(string $name, stdClass $configuration): AbstractType
    {
        if (!isset($configuration->path)) {
            throw new AttributeMissingException('Alias property must define path.');
        }

        return new AliasType((string)$configuration->path, $name);
    }
}
