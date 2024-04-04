<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Resolvers\AnalysisResolver;

use Elasticsearch\Mapping\Drivers\Factories\Analyzers\AnalyzerFactory;
use Elasticsearch\Mapping\Settings\Analysis;
use stdClass;

final class AnalyzerResolver
{
    /**
     * @throws \Elasticsearch\Mapping\Exceptions\AttributeMissingException
     */
    public function resolveAnalyzer(stdClass $analyzers, Analysis $analysis): void
    {
        foreach ((array)$analyzers as $name => $settings) {
            $analysis->addAnalyzer(AnalyzerFactory::create($name, $settings));
        }
    }
}
