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
use Elasticsearch\Mapping\Settings\MappingSettings;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\Dynamic;
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
            $this->resolveDynamic($mapping->$indexName->mappings, $index);
        }
        if (isset($mapping->$indexName->settings)) {
            $this->resolveIndexSettings($mapping->$indexName->settings, $index);
            $this->resolveAnalysis($mapping->$indexName->settings, $index);
        }

        return $index;
    }

    /**
     * Index settings can be placed under `settings.index` in the JSON, or flat in `settings` -
     * Elasticsearch accepts both variants.
     */
    private function resolveIndexSettings(stdClass $settings, Index $index): void
    {
        $sources = [$settings];
        if (isset($settings->index) && $settings->index instanceof stdClass) {
            // applied last on purpose, so the nested values overwrite the flat ones
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
            $this->resolveMappingSettings($source, $index);
        }
    }

    /**
     * `index.mapping.*` limits. ES returns them nested (`settings.index.mapping.total_fields.limit`),
     * a hand-written JSON may use the dotted key with or without the `index.` prefix. Values coming
     * from ES are strings.
     */
    private function resolveMappingSettings(stdClass $source, Index $index): void
    {
        foreach (MappingSettings::PATHS as $path => $definition) {
            $raw = $this->readMappingSetting($source, $path);
            if (null === $raw) {
                continue;
            }
            $mappingSettings = $index->getMappingSettings() ?? new MappingSettings();
            $mappingSettings->set(
                $path,
                'bool' === $definition['type'] ? filter_var($raw, FILTER_VALIDATE_BOOLEAN) : (int)$raw
            );
            $index->setMappingSettings($mappingSettings);
        }
    }

    /**
     * Reads one of the `index.mapping.*` settings given by its path below `mapping.`
     * (e.g. `total_fields.limit`): looks for the dotted key itself (with and without the `index.`
     * prefix) and then walks the path through nested objects.
     */
    private function readMappingSetting(stdClass $source, string $mappingPath): mixed
    {
        $path = 'mapping.' . $mappingPath;
        foreach ([$path, 'index.' . $path] as $dottedKey) {
            if (isset($source->$dottedKey) && is_scalar($source->$dottedKey)) {
                return $source->$dottedKey;
            }
        }

        $current = $source;
        foreach (explode('.', $path) as $segment) {
            if (!$current instanceof stdClass || !isset($current->$segment)) {
                return null;
            }
            $current = $current->$segment;
        }

        return is_scalar($current) ? $current : null;
    }

    /**
     * `mappings.dynamic` comes back from Elasticsearch as a string ("false", "strict"),
     * a hand-written JSON may hold a real boolean.
     */
    private function resolveDynamic(stdClass $mappings, Index $index): void
    {
        if (!isset($mappings->dynamic)) {
            return;
        }
        $dynamic = $mappings->dynamic;
        if (is_bool($dynamic)) {
            $dynamic = $dynamic ? 'true' : 'false';
        }
        if (is_string($dynamic) && null !== $case = Dynamic::tryFrom($dynamic)) {
            $index->setDynamic($case);
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
                $this->tokenizerResolver->resolveTokenizer($mappings->analysis->tokenizer, $analysis);
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
