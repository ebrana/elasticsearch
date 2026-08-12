<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Resolvers\AnalysisResolver;

use Elasticsearch\Mapping\Drivers\Factories\Analyzers\AnalyzerFactory;
use Elasticsearch\Mapping\Drivers\Factories\Analyzers\BuiltInAnalyzerFactory;
use Elasticsearch\Mapping\Settings\Analysis;
use Elasticsearch\Mapping\Settings\AnalyzerInterface;
use stdClass;

final class AnalyzerResolver
{
    /**
     * @throws \Elasticsearch\Mapping\Exceptions\AttributeMissingException
     */
    public function resolveAnalyzer(stdClass $analyzers, Analysis $analysis): void
    {
        /** @var stdClass $settings */
        foreach ((array)$analyzers as $name => $settings) {
            $analysis->addAnalyzer($this->createAnalyzer((string)$name, $settings));
        }
    }

    /**
     * Bez `type` (nebo s `type: custom`) jde o vlastni analyzer skladany z tokenizeru a filtru.
     * Nerozpoznany `type` se resi stejne jako dosud - jako custom analyzer.
     *
     * @throws \Elasticsearch\Mapping\Exceptions\AttributeMissingException
     */
    private function createAnalyzer(string $name, stdClass $settings): AnalyzerInterface
    {
        $type = isset($settings->type) ? (string)$settings->type : null;

        if (null !== $type && 'custom' !== $type && BuiltInAnalyzerFactory::supports($type)) {
            return BuiltInAnalyzerFactory::create($name, $type, $settings);
        }

        return AnalyzerFactory::create($name, $settings);
    }
}
