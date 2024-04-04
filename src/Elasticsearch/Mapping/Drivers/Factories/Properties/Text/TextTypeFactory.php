<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Text;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\Text\TextType;
use stdClass;

class TextTypeFactory implements PropertyFactoryInterface
{
    public static function create(string $name, stdClass $configuration): TextType
    {
        $textType = new TextType();
        $textType->setName($name);

        return $textType;
    }
}
