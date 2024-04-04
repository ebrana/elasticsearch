<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common\Keywords;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\Keywords\Enums\IndexOptions;
use Elasticsearch\Mapping\Types\Common\Keywords\Enums\Similary;
use Elasticsearch\Mapping\Types\Helpers\MetadataTrait;
use Elasticsearch\Mapping\Types\Helpers\MultiFieldsInterface;
use Elasticsearch\Mapping\Types\Helpers\MultiFieldsTrait;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
final class KeywordType extends AbstractType implements MultiFieldsInterface
{
    use MetadataTrait;
    use MultiFieldsTrait;

    /**
     * @param AbstractType[]|null $fields
     */
    public function __construct(
        private bool $doc_values = true,
        private bool $eager_global_ordinals = false,
        private int $ignore_above = 2147483647,
        private bool $index = true,
        // positions, docs, freqs, offsets @see https://www.elastic.co/guide/en/elasticsearch/reference/current/index-options.html
        private IndexOptions $index_options = IndexOptions::POSITIONS,
        private bool $norms = false,
        private bool $store = false,
        // boolean or default BM25 @see https://www.elastic.co/guide/en/elasticsearch/reference/current/similarity.html
        private Similary $similarity = Similary::BM25,
        private ?string $null_value = null,
        private ?string $normalizer = null,
        ?array $fields = null,
        ?string $name = null,
        ?string $context = null,
    ) {
        parent::__construct();

        $this->context = $context;
        $this->type = 'keyword';

        if ($name && $name !== '') {
            $this->setName($name);
        }

        $this->createFields($fields);
    }

    public function isDocValues(): bool
    {
        return $this->doc_values;
    }

    public function setDocValues(bool $doc_values): void
    {
        $this->doc_values = $doc_values;
    }

    public function isEagerGlobalOrdinals(): bool
    {
        return $this->eager_global_ordinals;
    }

    public function setEagerGlobalOrdinals(bool $eager_global_ordinals): void
    {
        $this->eager_global_ordinals = $eager_global_ordinals;
    }

    public function getIgnoreAbove(): int
    {
        return $this->ignore_above;
    }

    public function setIgnoreAbove(int $ignore_above): void
    {
        $this->ignore_above = $ignore_above;
    }

    public function isIndex(): bool
    {
        return $this->index;
    }

    public function setIndex(bool $index): void
    {
        $this->index = $index;
    }

    public function getIndexOptions(): IndexOptions
    {
        return $this->index_options;
    }

    public function setIndexOptions(IndexOptions $index_options): void
    {
        $this->index_options = $index_options;
    }

    public function isNorms(): bool
    {
        return $this->norms;
    }

    public function setNorms(bool $norms): void
    {
        $this->norms = $norms;
    }

    public function getNullValue(): ?string
    {
        return $this->null_value;
    }

    public function setNullValue(?string $null_value): void
    {
        $this->null_value = $null_value;
    }

    public function isStore(): bool
    {
        return $this->store;
    }

    public function setStore(bool $store): void
    {
        $this->store = $store;
    }

    public function getSimilarity(): Similary
    {
        return $this->similarity;
    }

    public function setSimilarity(Similary $similarity): void
    {
        $this->similarity = $similarity;
    }

    public function getNormalizer(): ?string
    {
        return $this->normalizer;
    }

    public function setNormalizer(?string $normalizer): void
    {
        $this->normalizer = $normalizer;
    }

    public function getCollection(): ArrayCollection
    {
        $collection = parent::getCollection();
        $this->extendCollectionByFields($collection);

        if (false === $this->doc_values) {
            $collection->set('doc_values', $this->doc_values);
        }

        if ($this->eager_global_ordinals) {
            $collection->set('eager_global_ordinals', $this->eager_global_ordinals);
        }

        if ($this->ignore_above !== 2147483647) {
            $collection->set('ignore_above', $this->ignore_above);
        }

        if (false === $this->index) {
            $collection->set('index', $this->index);
        }

        if ($this->index_options !== IndexOptions::POSITIONS) {
            $collection->set('index_options', $this->index_options->value);
        }

        if ($this->norms) {
            $collection->set('norms', $this->norms);
        }

        if ($this->store) {
            $collection->set('store', $this->store);
        }

        if ($this->similarity !== Similary::BM25) {
            $collection->set('similarity', $this->similarity->value);
        }

        if ($this->null_value) {
            $collection->set('null_value', $this->null_value);
        }

        if ($this->normalizer) {
            $collection->set('normalizer', $this->normalizer);
        }

        return $collection;
    }
}
