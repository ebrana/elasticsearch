<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Text;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Text\CompletionType;
use stdClass;

class CompletionTypeFactory implements PropertyFactoryInterface
{
    public static function create(string $name, stdClass $configuration): AbstractType
    {
        $completionType = new CompletionType();
        $completionType->setName($name);

        if (isset($configuration->analyzer)) {
            $completionType->setAnalyzer((string)$configuration->analyzer);
        }
        if (isset($configuration->search_analyzer)) {
            $completionType->setSearchAnalyzer((string)$configuration->search_analyzer);
        }
        if (isset($configuration->preserve_separators)) {
            $completionType->setPreserveSeparators((bool)$configuration->preserve_separators);
        }
        if (isset($configuration->preserve_position_increments)) {
            $completionType->setPreservePositionIncrements((bool)$configuration->preserve_position_increments);
        }
        if (isset($configuration->max_input_length)) {
            $completionType->setMaxInputLength((int)$configuration->max_input_length);
        }
        if (isset($configuration->contexts) && is_array($configuration->contexts)) {
            /** @var array<int, array<string, mixed>> $contexts */
            $contexts = json_decode(json_encode($configuration->contexts, JSON_THROW_ON_ERROR), true);
            $completionType->setContexts($contexts);
        }

        return $completionType;
    }
}
