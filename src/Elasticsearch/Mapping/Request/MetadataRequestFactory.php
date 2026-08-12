<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Request;

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
     * @return array<string, mixed>|null
     */
    private function resolveSettings(Index $index): ?array
    {
        // nastaveni indexu se posila i kdyz index nema zadnou analysis
        $settings = $this->provideIndexSettings($index);
        $analysis = $index->getAnalysis();

        if (null !== $analysis) {
            $settings['analysis'] = ['analyzer' => []];
            $this->provideAnalyzers($analysis, $settings);
            $this->provideFilters($analysis, $settings);
            $this->provideCharacterFilters($analysis, $settings);
            $this->provideTokenizers($analysis, $settings);
            $this->provideNormalizers($analysis, $settings);
        }

        return [] === $settings ? null : $settings;
    }

    /**
     * Elasticsearch bere tyhle klice jak naplocho v `settings`, tak pod `settings.index` -
     * knihovna je posila naplocho.
     *
     * @return array<string, mixed>
     */
    private function provideIndexSettings(Index $index): array
    {
        $settings = ['max_result_window' => $index->getMaxResultWindow()];

        foreach ([
            'number_of_shards'   => $index->getNumberOfShards(),
            'number_of_replicas' => $index->getNumberOfReplicas(),
            'refresh_interval'   => $index->getRefreshInterval(),
            'max_ngram_diff'     => $index->getMaxNgramDiff(),
            'max_shingle_diff'   => $index->getMaxShingleDiff(),
        ] as $key => $value) {
            if (null !== $value) {
                $settings[$key] = $value;
            }
        }

        return $settings;
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function provideNormalizers(Analysis $analysis, array &$settings): void
    {
        $normalizers = $analysis->getNormalizers();
        if ($normalizers->count() > 0) {
            $settings['analysis']['normalizer'] = [];

            /** @var \Elasticsearch\Mapping\Settings\Normalizer $normalizer */
            foreach ($normalizers as $normalizer) {
                $settings['analysis']['normalizer'][$normalizer->getName()] = $normalizer->toArray();
            }
        }
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function provideAnalyzers(Analysis $analysis, array &$settings): void
    {
        $analyzers = $analysis->getAnalyzers();

        /** @var \Elasticsearch\Mapping\Settings\AnalyzerInterface $analyzer */
        foreach ($analyzers as $analyzer) {
            $settings['analysis']['analyzer'][$analyzer->getName()] = $analyzer->toArray();
        }
    }

    /**
     * @param array<string, mixed> $settings
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
            $settings['analysis']['char_filter'] = [];

            /** @var \Elasticsearch\Mapping\Settings\AbstractCharactedFilter $filter */
            foreach ($filters as $filter) {
                $settings['analysis']['char_filter'][$filter->getName()] = $filter->toArray();
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
