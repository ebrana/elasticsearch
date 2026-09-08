<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Keywords;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\Keywords\Enums\IndexOptions;
use Elasticsearch\Mapping\Types\Common\Keywords\Enums\Similary;
use Elasticsearch\Mapping\Types\Common\Keywords\KeywordType;
use stdClass;

class KeywordTypeFactory implements PropertyFactoryInterface
{
    /**
     * @param stdClass&object{
     *     doc_values?: bool,
     *     eager_global_ordinals?: bool,
     *     ignore_above?: int,
     *     index?: bool,
     *     index_options?: string,
     *     norms?: bool,
     *     store?: bool,
     *     similarity?: string,
     *     null_value?: string,
     *     normalizer?: string,
     *     copy_to?: string
     * } $configuration
     */
    public static function create(string $name, stdClass $configuration): AbstractType
    {
        $keywordType = new KeywordType();
        $keywordType->setName($name);

        if (isset($configuration->doc_values)) {
            $keywordType->setDocValues((bool)$configuration->doc_values);
        }

        if (isset($configuration->eager_global_ordinals)) {
            $keywordType->setEagerGlobalOrdinals((bool)$configuration->eager_global_ordinals);
        }

        if (isset($configuration->ignore_above)) {
            $keywordType->setIgnoreAbove((int)$configuration->ignore_above);
        }

        if (isset($configuration->index)) {
            $keywordType->setIndex((bool)$configuration->index);
        }

        if (isset($configuration->index_options)) {
            $indexOptions = IndexOptions::tryFrom($configuration->index_options);
            if ($indexOptions) {
                $keywordType->setIndexOptions($indexOptions);
            }
        }

        if (isset($configuration->norms)) {
            $keywordType->setNorms((bool)$configuration->norms);
        }

        if (isset($configuration->store)) {
            $keywordType->setStore((bool)$configuration->store);
        }

        if (isset($configuration->similarity)) {
            $similarity = Similary::tryFrom($configuration->similarity);
            if ($similarity) {
                $keywordType->setSimilarity($similarity);
            }
        }

        if (isset($configuration->null_value)) {
            $keywordType->setNullValue($configuration->null_value);
        }

        if (isset($configuration->normalizer)) {
            $keywordType->setNormalizer($configuration->normalizer);
        }

        if (isset($configuration->copy_to)) {
            $keywordType->setCopyTo($configuration->copy_to);
        }

        return $keywordType;
    }
}
