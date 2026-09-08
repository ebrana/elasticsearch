<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Text;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\Text\TextType;
use stdClass;

class TextTypeFactory implements PropertyFactoryInterface
{
    /**
     * @param stdClass&object{
     *     analyzer?: string,
     *     search_analyzer?: string,
     *     search_quote_analyzer?: string,
     *     eager_global_ordinals?: bool,
     *     fielddata?: bool,
     *     index?: bool,
     *     index_options?: string,
     *     index_phrases?: bool,
     *     index_prefixes?: object{min_chars?: int, max_chars?: int},
     *     norms?: bool,
     *     position_increment_gap?: int,
     *     similarity?: string,
     *     term_vector?: string,
     *     store?: bool,
     *     copy_to?: string
     * } $configuration
     */
    public static function create(string $name, stdClass $configuration): TextType
    {
        $textType = new TextType();
        $textType->setName($name);

        if (isset($configuration->analyzer)) {
            $textType->setAnalyzer($configuration->analyzer);
        }

        if (isset($configuration->search_analyzer)) {
            $textType->setSearchAnalyzer($configuration->search_analyzer);
        }

        if (isset($configuration->search_quote_analyzer)) {
            $textType->setSearchQuoteAnalyzer($configuration->search_quote_analyzer);
        }

        if (isset($configuration->eager_global_ordinals)) {
            $textType->setEagerGlobalOrdinals((bool)$configuration->eager_global_ordinals);
        }

        if (isset($configuration->fielddata)) {
            $textType->setFielddata((bool)$configuration->fielddata);
        }

        if (isset($configuration->index)) {
            $textType->setIndex((bool)$configuration->index);
        }

        if (isset($configuration->index_options)) {
            $textType->setIndexOptions($configuration->index_options);
        }

        if (isset($configuration->index_phrases)) {
            $textType->setIndexPhrases((bool)$configuration->index_phrases);
        }

        if (isset($configuration->index_prefixes->min_chars)) {
            $textType->setIndexPrefixesMinChars((int)$configuration->index_prefixes->min_chars);
        }

        if (isset($configuration->index_prefixes->max_chars)) {
            $textType->setIndexPrefixesMaxChars((int)$configuration->index_prefixes->max_chars);
        }

        if (isset($configuration->norms)) {
            $textType->setNorms((bool)$configuration->norms);
        }

        if (isset($configuration->position_increment_gap)) {
            $textType->setPositionIncrementGap((int)$configuration->position_increment_gap);
        }

        if (isset($configuration->similarity)) {
            $textType->setSimilarity($configuration->similarity);
        }

        if (isset($configuration->term_vector)) {
            $textType->setTermVector($configuration->term_vector);
        }

        if (isset($configuration->store)) {
            $textType->setStore((bool)$configuration->store);
        }

        if (isset($configuration->copy_to)) {
            $textType->setCopyTo($configuration->copy_to);
        }

        return $textType;
    }
}
