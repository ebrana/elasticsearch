<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric;

use Elasticsearch\Mapping\Types\Common\Numeric\AbstractNumericType;
use stdClass;

trait NumericConfigurationTrait
{
    /**
     * Reads the parameters shared by the numeric types: coerce, doc_values, ignored_malformed, index, store, null_value.
     */
    private static function applyConfiguration(AbstractNumericType $type, stdClass $configuration): void
    {
        if (isset($configuration->coerce)) {
            $type->setCoerce((bool)$configuration->coerce);
        }

        if (isset($configuration->doc_values)) {
            $type->setDocValues((bool)$configuration->doc_values);
        }

        if (isset($configuration->ignored_malformed)) {
            $type->setIgnoredMalformed((bool)$configuration->ignored_malformed);
        }

        if (isset($configuration->index)) {
            $type->setIndex((bool)$configuration->index);
        }

        if (isset($configuration->store)) {
            $type->setStore((bool)$configuration->store);
        }

        if (isset($configuration->null_value)) {
            $type->setNullValue((string)$configuration->null_value);
        }
    }
}
