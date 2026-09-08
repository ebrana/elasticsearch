<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties;

use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\BooleanType;
use stdClass;

class BooleanTypeFactory implements PropertyFactoryInterface
{
    /**
     * @param stdClass&object{
     *     doc_values?: bool,
     *     store?: bool,
     *     index?: bool,
     *     null_value?: bool|string
     * } $configuration
     */
    public static function create(string $name, stdClass $configuration): AbstractType
    {
        $booleanType = new BooleanType();
        $booleanType->setName($name);

        if (isset($configuration->doc_values)) {
            $booleanType->setDocValues((bool)$configuration->doc_values);
        }

        if (isset($configuration->store)) {
            $booleanType->setStore((bool)$configuration->store);
        }

        if (isset($configuration->index)) {
            $booleanType->setIndex((bool)$configuration->index);
        }

        if (isset($configuration->null_value)) {
            $booleanType->setNullValue(
                is_bool($configuration->null_value)
                    ? ($configuration->null_value ? 'true' : 'false')
                    : (string)$configuration->null_value
            );
        }

        return $booleanType;
    }
}
