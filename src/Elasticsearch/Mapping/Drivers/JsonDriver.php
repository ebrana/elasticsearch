<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers;

use Elasticsearch\Mapping\Drivers\Resolvers\AnalysisResolver\AnalyzerResolver;
use Elasticsearch\Mapping\Drivers\Resolvers\AnalysisResolver\CharacterFilterResolver;
use Elasticsearch\Mapping\Drivers\Resolvers\AnalysisResolver\FiltersResolver;
use Elasticsearch\Mapping\Drivers\Resolvers\AnalysisResolver\NormalizerResolver;
use Elasticsearch\Mapping\Drivers\Resolvers\AnalysisResolver\TokenizerResolver;
use Elasticsearch\Mapping\Drivers\Resolvers\PropertiesResolver\PropertiesResolver;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Settings\Analysis;
use RuntimeException;
use stdClass;

class JsonDriver implements DriverInterface
{
    private PropertiesResolver $propertiesResolver;
    private FiltersResolver $filtersResolver;
    private TokenizerResolver $tokenizerResolver;
    private AnalyzerResolver $analyzerResolver;
    private CharacterFilterResolver $characterFilterResolver;
    private NormalizerResolver $normalizerResolver;

    public function __construct()
    {
        $this->propertiesResolver = new PropertiesResolver();
        $this->filtersResolver = new FiltersResolver();
        $this->tokenizerResolver = new TokenizerResolver();
        $this->analyzerResolver = new AnalyzerResolver();
        $this->characterFilterResolver = new CharacterFilterResolver();
        $this->normalizerResolver = new NormalizerResolver();
    }

    /**
     * @param string $source Path of source json file
     * @throws \Elasticsearch\Mapping\Exceptions\DuplicityPropertyException
     * @throws \JsonException
     * @throws \Elasticsearch\Mapping\Exceptions\AttributeMissingException
     */
    public function loadMetadata(string $source): Index
    {
        if (false === is_readable($source)) {
            throw new RuntimeException(sprintf('Invalid path "%s"', $source));
        }

        $json = file_get_contents($source);
        if (false === is_string($json)) {
            throw new RuntimeException(sprintf('Invalid content in file "%s"', $source));
        }

        /** @var stdClass $mapping */
        $mapping = json_decode($json, false, depth: 530, flags: JSON_THROW_ON_ERROR);
        $indexName = (string)key((array)$mapping);
        $index = new Index($indexName);

        if (isset($mapping->$indexName->mappings)) {
            $this->propertiesResolver->resolveProperties($mapping->$indexName->mappings, $index);
        }
        if (isset($mapping->$indexName->settings)) {
            $this->resolveIndexSettings($mapping->$indexName->settings, $index);
            $this->resolveAnalysis($mapping->$indexName->settings, $index);
        }

        return $index;
    }

    /**
     * Nastaveni indexu muze byt v JSONu pod `settings.index`, nebo naplocho v `settings` -
     * Elasticsearch bere obe varianty.
     */
    private function resolveIndexSettings(stdClass $settings, Index $index): void
    {
        $sources = [$settings];
        if (isset($settings->index) && $settings->index instanceof stdClass) {
            // vnorena varianta ma prednost
            $sources[] = $settings->index;
        }

        foreach ($sources as $source) {
            if (isset($source->max_result_window)) {
                $index->setMaxResultWindow((int)$source->max_result_window);
            }
            if (isset($source->number_of_shards)) {
                $index->setNumberOfShards((int)$source->number_of_shards);
            }
            if (isset($source->number_of_replicas)) {
                $index->setNumberOfReplicas((int)$source->number_of_replicas);
            }
            if (isset($source->refresh_interval)) {
                $index->setRefreshInterval((string)$source->refresh_interval);
            }
            if (isset($source->max_ngram_diff)) {
                $index->setMaxNgramDiff((int)$source->max_ngram_diff);
            }
            if (isset($source->max_shingle_diff)) {
                $index->setMaxShingleDiff((int)$source->max_shingle_diff);
            }
        }
    }

    /**
     * @throws \Elasticsearch\Mapping\Exceptions\AttributeMissingException
     */
    private function resolveAnalysis(stdClass $mappings, Index $index): void
    {
        if (isset($mappings->analysis)) {
            $analysis = new Analysis();
            // resolve filters
            if (isset($mappings->analysis->filter)) {
                $this->filtersResolver->resolveFilters($mappings->analysis->filter, $analysis);
            }
            // resolve analyzer
            if (isset($mappings->analysis->analyzer)) {
                $this->analyzerResolver->resolveAnalyzer($mappings->analysis->analyzer, $analysis);
            }
            // resolve tokenizer
            if (isset($mappings->analysis->tokenizer)) {
                $this->tokenizerResolver->resolvetTokenizer($mappings->analysis->tokenizer, $analysis);
            }
            // resolve character filter
            if (isset($mappings->analysis->char_filter)) {
                $this->characterFilterResolver->resolveFilters($mappings->analysis->char_filter, $analysis);
            }
            // resolve normalizer
            if (isset($mappings->analysis->normalizer)) {
                $this->normalizerResolver->resolveNormalizer($mappings->analysis->normalizer, $analysis);
            }

            $index->setAnalysis($analysis);
        }
    }
}
