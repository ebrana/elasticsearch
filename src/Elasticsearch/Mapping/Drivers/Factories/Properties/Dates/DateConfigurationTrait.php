<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Dates;

use Elasticsearch\Mapping\Types\Common\Dates\DateNanoType;
use Elasticsearch\Mapping\Types\Common\Dates\DateType;
use stdClass;

trait DateConfigurationTrait
{
    /**
     * Reads format, locale, null_value, index, doc_values, store and ignore_malformed.
     */
    private static function applyConfiguration(DateType|DateNanoType $type, stdClass $configuration): void
    {
        if (isset($configuration->format)) {
            $type->setFormat((string)$configuration->format);
        }

        if (isset($configuration->locale)) {
            $type->setLocale((string)$configuration->locale);
        }

        if (isset($configuration->null_value)) {
            $type->setNullValue((string)$configuration->null_value);
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

        if (isset($configuration->ignore_malformed)) {
            $type->setIgnoreMalformed((bool)$configuration->ignore_malformed);
        }
    }
}
