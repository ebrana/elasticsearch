<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Request;

use Elasticsearch\Mapping\Exceptions\EmptyIndexNameException;
use Elasticsearch\Mapping\Exceptions\MappingJsonCreateException;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Settings\Analysis;
use JsonException;

class MetadataRequestFactory
{
    /**
     * @throws \Elasticsearch\Mapping\Exceptions\MappingJsonCreateException
     */
    public function create(Index $index): MetadataRequest
    {
        $metadaRequest = new MetadataRequest($index);
        $record = [
            'mappings' => $this->resolveProperties($index),
        ];
        $settings = $this->resolveSettings($index);
        if (null !== $settings) {
            $record['settings'] = $settings;
        }
        try {
            $mappingJson = json_encode($record, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new MappingJsonCreateException($e);
        }

        $metadaRequest->setMappingJson($mappingJson);

        return $metadaRequest;
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveProperties(Index $index): array
    {
        $properties = $index->getProperties();
        $indexProperties = [
            'properties' => []
        ];

        foreach ($properties as $property) {
            $indexProperties['properties'][$property->getName()] = $property->getCollection()->toArray();
        }

        return $indexProperties;
    }

    /**
     * @return array<string, array<string, array<string, array<string, array<string>|string>>>>|null
     */
    private function resolveSettings(Index $index): ?array
    {
        $analysis = $index->getAnalysis();
        if (null === $analysis) {
            return null;
        }

        $settings = [
            'analysis' => [
                'analyzer' => [],
            ],
            'max_result_window' => $index->getMaxResultWindow(),
        ];
        $this->provideAnalyzers($analysis, $settings);
        $this->provideFilters($analysis, $settings);
        $this->provideCharacterFilters($analysis, $settings);
        $this->provideTokenizers($analysis, $settings);

        return $settings;
    }

    /**
     * @param array<string, array<string, array<string, array<string, array<string>|string>>>> $settings
     */
    private function provideAnalyzers(Analysis $analysis, array &$settings): void
    {
        $analyzers = $analysis->getAnalyzers();

        /** @var \Elasticsearch\Mapping\Settings\Analyzer $analyzer */
        foreach ($analyzers as $analyzer) {
            $settings['analysis']['analyzer'][$analyzer->getName()] = $analyzer->toArray();
        }
    }

    /**
     * @param array<string, array<string, array<string, array<string, array<string>|string>>>> $settings
     */
    private function provideFilters(Analysis $analysis, array &$settings): void
    {
        $filters = $analysis->getFilters();
        if ($filters->count() > 0) {
            $settings['analysis']['filter'] = [];

            /** @var \Elasticsearch\Mapping\Settings\AbstractFilter $filter */
            foreach ($filters as $filter) {
                $settings['analysis']['filter'][$filter->getName()] = $filter->toArray();
            }
        }
    }

    /**
     * @param array<string, array<string, array<string, array<string, array<string>|string>>>> $settings
     */
    private function provideCharacterFilters(Analysis $analysis, array &$settings): void
    {
        $filters = $analysis->getCharacterFilters();
        if ($filters->count() > 0) {
            $settings['analysis']['character_filter'] = [];

            /** @var \Elasticsearch\Mapping\Settings\AbstractFilter $filter */
            foreach ($filters as $filter) {
                $settings['analysis']['character_filter'][$filter->getName()] = $filter->toArray();
            }
        }
    }

    /**
     * @param array<string, array<string, array<string, array<string, array<string>|string>>>> $settings
     */
    private function provideTokenizers(Analysis $analysis, array &$settings): void
    {
        $tokenizers = $analysis->getTokenizers();
        if ($tokenizers->count() > 0) {
            $settings['analysis']['tokenizer'] = [];

            /** @var \Elasticsearch\Mapping\Settings\AbstractTokenizer $tokenizer */
            foreach ($tokenizers as $tokenizer) {
                $settings['analysis']['tokenizer'][$tokenizer->getName()] = $tokenizer->toArray();
            }
        }
    }
}
