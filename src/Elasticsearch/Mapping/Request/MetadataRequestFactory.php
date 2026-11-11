<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Request;

use Elasticsearch\Mapping\Exceptions\MappingJsonCreateException;
use Elasticsearch\Mapping\Exceptions\SourceOnlyRequiresDynamicFalseException;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Settings\Analysis;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\Dynamic;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;
use Elasticsearch\Mapping\Types\SourceOnly;
use JsonException;

class MetadataRequestFactory
{
    /**
     * @throws \Elasticsearch\Mapping\Exceptions\MappingJsonCreateException
     * @throws \Elasticsearch\Mapping\Exceptions\SourceOnlyRequiresDynamicFalseException
     */
    public function create(Index $index): MetadataRequest
    {
        $this->assertSourceOnlyFieldsStayOutOfTheMapping($index->getProperties(), $index->getDynamic(), '');

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
            if ($property instanceof SourceOnly) {
                continue; // lives in _source only
            }
            $indexProperties['properties'][$property->getName()] = $property->getCollection()->toArray();
        }

        // `true` is the ES default, sent only when changed (same rule as ObjectType)
        if ($index->getDynamic() !== Dynamic::TRUE) {
            $indexProperties['dynamic'] = $index->getDynamic()->value;
        }

        return $indexProperties;
    }

    /**
     * A SourceOnly field only makes sense where the effective `dynamic` is `false`: `true` would map
     * it dynamically, `strict` would reject the document. `Dynamic::TRUE` on an ObjectType is the
     * default the library never sends, so it inherits the parent's value.
     *
     * @param iterable<AbstractType> $properties
     * @throws \Elasticsearch\Mapping\Exceptions\SourceOnlyRequiresDynamicFalseException
     */
    private function assertSourceOnlyFieldsStayOutOfTheMapping(
        iterable $properties,
        Dynamic $effectiveDynamic,
        string $path
    ): void {
        foreach ($properties as $property) {
            $fieldPath = '' === $path ? $property->getName() : $path . '.' . $property->getName();

            if ($property instanceof SourceOnly) {
                if (Dynamic::FALSE !== $effectiveDynamic) {
                    throw new SourceOnlyRequiresDynamicFalseException($fieldPath, $effectiveDynamic);
                }
                continue;
            }

            if ($property instanceof ObjectType) {
                $dynamic = Dynamic::TRUE === $property->getDynamic() ? $effectiveDynamic : $property->getDynamic();
                $this->assertSourceOnlyFieldsStayOutOfTheMapping($property->getProperties(), $dynamic, $fieldPath);
            }
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function resolveSettings(Index $index): ?array
    {
        // index settings are sent even when the index has no analysis at all
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
     * Elasticsearch accepts these keys both flat in `settings` and under `settings.index` -
     * the library sends them flat. Dotted keys (`mapping.total_fields.limit`) are expanded
     * by Elasticsearch the same way.
     *
     * @return array<string, mixed>
     */
    private function provideIndexSettings(Index $index): array
    {
        $settings = ['max_result_window' => $index->getMaxResultWindow()];

        $optionalSettings = [
            'number_of_shards'   => $index->getNumberOfShards(),
            'number_of_replicas' => $index->getNumberOfReplicas(),
            'refresh_interval'   => $index->getRefreshInterval(),
            'max_ngram_diff'     => $index->getMaxNgramDiff(),
            'max_shingle_diff'   => $index->getMaxShingleDiff(),
        ];

        foreach ($optionalSettings as $key => $value) {
            if (null !== $value) {
                $settings[$key] = $value;
            }
        }

        $mappingSettings = $index->getMappingSettings();
        if (null !== $mappingSettings) {
            foreach ($mappingSettings->toArray() as $path => $value) {
                $settings['mapping.' . $path] = $value;
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

            /** @var \Elasticsearch\Mapping\Settings\AbstractCharacterFilter $filter */
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
