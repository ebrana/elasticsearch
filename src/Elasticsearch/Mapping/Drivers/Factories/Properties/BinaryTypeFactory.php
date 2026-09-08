<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties;

use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\BinaryType;
use stdClass;

class BinaryTypeFactory implements PropertyFactoryInterface
{
    /**
     * @param stdClass&object{doc_values?: bool, store?: bool} $configuration
     */
    public static function create(string $name, stdClass $configuration): AbstractType
    {
        $binaryType = new BinaryType(
            (bool)($configuration->doc_values ?? false),
            (bool)($configuration->store ?? false),
            $name
        );

        return $binaryType;
    }
}
