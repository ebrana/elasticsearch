<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Keywords;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\Keywords\IcuCollationKeywordType;
use stdClass;

class IcuCollationKeywordTypeFactory implements PropertyFactoryInterface
{
    public static function create(string $name, stdClass $configuration): AbstractType
    {
        $type = new IcuCollationKeywordType();
        $type->setName($name);

        foreach (['language', 'country', 'variant', 'strength', 'decomposition', 'alternate',
                  'case_first', 'null_value'] as $key) {
            if (isset($configuration->$key)) {
                $setter = 'set' . str_replace('_', '', ucwords($key, '_'));
                $type->$setter((string)$configuration->$key);
            }
        }

        if (isset($configuration->case_level)) {
            $type->setCaseLevel((bool)$configuration->case_level);
        }
        if (isset($configuration->numeric)) {
            $type->setNumeric((bool)$configuration->numeric);
        }
        if (isset($configuration->index)) {
            $type->setIndex((bool)$configuration->index);
        }
        if (isset($configuration->doc_values)) {
            $type->setDocValues((bool)$configuration->doc_values);
        }
        if (isset($configuration->store)) {
            $type->setStore((bool)$configuration->store);
        }

        return $type;
    }
}
