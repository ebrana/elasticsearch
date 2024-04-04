<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Analyzers;

use Elasticsearch\Mapping\Exceptions\AttributeMissingException;
use Elasticsearch\Mapping\Settings\Analyzer;
use stdClass;

final class AnalyzerFactory implements AnalyzerFactoryInterface
{
    /**
     * @throws \Elasticsearch\Mapping\Exceptions\AttributeMissingException
     */
    public static function create(string $name, stdClass $configuration): Analyzer
    {
        if (!isset($configuration->tokenizer)) {
            throw new AttributeMissingException('Analyzer must define tokenizer.');
        }
        $filters = isset($configuration->filter) && is_array($configuration->filter) ? $configuration->filter : [];

        return new Analyzer($name, $configuration->tokenizer, $filters);
    }
}
