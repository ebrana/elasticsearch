<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Text;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Helpers\Metadata;
use Elasticsearch\Mapping\Types\Helpers\MetadataTrait;
use Elasticsearch\Mapping\Types\Helpers\MultiFieldsInterface;
use Elasticsearch\Mapping\Types\Helpers\MultiFieldsTrait;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class TextType extends AbstractType implements MultiFieldsInterface
{
    use MetadataTrait;
    use MultiFieldsTrait;

    /**
     * @param AbstractType[]|null $fields
     */
    public function __construct(
        private ?string $analyzer = null,
        private bool $eager_global_ordinals = false,
        private bool $fielddata = false,
        private bool $index = true,
        private string $index_options = 'positions',
        private bool $index_phrases = false,
        private bool $norms = true,
        private int $position_increment_gap = 100,
        private bool $store = false,
        private ?string $search_analyzer = null,
        private ?string $search_quote_analyzer = null,
        private string $similarity = 'BM25',
        private string $term_vector = 'no',
        private ?int $index_prefixes_min_chars = null,
        private ?int $index_prefixes_max_chars = null,
        private ?string $copy_to = null,
        ?string $name = null,
        ?Metadata $meta = null,
        ?array $fields = null,
        ?string $context = null,
    ) {
        parent::__construct();

        $this->context = $context;
        $this->type = 'text';
        $this->meta = $meta;

        if (null !== $name && $name !== '') {
            $this->setName($name);
        }

        $this->createFields($fields);
    }

    public function getAnalyzer(): ?string
    {
        return $this->analyzer;
    }

    public function setAnalyzer(?string $analyzer): void
    {
        $this->analyzer = $analyzer;
    }

    public function isEagerGlobalOrdinals(): bool
    {
        return $this->eager_global_ordinals;
    }

    public function setEagerGlobalOrdinals(bool $eager_global_ordinals): void
    {
        $this->eager_global_ordinals = $eager_global_ordinals;
    }

    public function isFielddata(): bool
    {
        return $this->fielddata;
    }

    public function setFielddata(bool $fielddata): void
    {
        $this->fielddata = $fielddata;
    }

    public function isIndex(): bool
    {
        return $this->index;
    }

    public function setIndex(bool $index): void
    {
        $this->index = $index;
    }

    public function getIndexOptions(): string
    {
        return $this->index_options;
    }

    public function setIndexOptions(string $index_options): void
    {
        $this->index_options = $index_options;
    }

    public function isIndexPhrases(): bool
    {
        return $this->index_phrases;
    }

    public function setIndexPhrases(bool $index_phrases): void
    {
        $this->index_phrases = $index_phrases;
    }

    public function isNorms(): bool
    {
        return $this->norms;
    }

    public function setNorms(bool $norms): void
    {
        $this->norms = $norms;
    }

    public function getPositionIncrementGap(): int
    {
        return $this->position_increment_gap;
    }

    public function setPositionIncrementGap(int $position_increment_gap): void
    {
        $this->position_increment_gap = $position_increment_gap;
    }

    public function isStore(): bool
    {
        return $this->store;
    }

    public function setStore(bool $store): void
    {
        $this->store = $store;
    }

    public function getSearchAnalyzer(): ?string
    {
        return $this->search_analyzer;
    }

    public function setSearchAnalyzer(?string $search_analyzer): void
    {
        $this->search_analyzer = $search_analyzer;
    }

    public function getSearchQuoteAnalyzer(): ?string
    {
        return $this->search_quote_analyzer;
    }

    public function setSearchQuoteAnalyzer(?string $search_quote_analyzer): void
    {
        $this->search_quote_analyzer = $search_quote_analyzer;
    }

    public function getSimilarity(): string
    {
        return $this->similarity;
    }

    public function setSimilarity(string $similarity): void
    {
        $this->similarity = $similarity;
    }

    public function getTermVector(): string
    {
        return $this->term_vector;
    }

    public function setTermVector(string $term_vector): void
    {
        $this->term_vector = $term_vector;
    }

    public function getIndexPrefixesMinChars(): ?int
    {
        return $this->index_prefixes_min_chars;
    }

    public function setIndexPrefixesMinChars(?int $index_prefixes_min_chars): void
    {
        $this->index_prefixes_min_chars = $index_prefixes_min_chars;
    }

    public function getIndexPrefixesMaxChars(): ?int
    {
        return $this->index_prefixes_max_chars;
    }

    public function setIndexPrefixesMaxChars(?int $index_prefixes_max_chars): void
    {
        $this->index_prefixes_max_chars = $index_prefixes_max_chars;
    }

    public function getCopyTo(): ?string
    {
        return $this->copy_to;
    }

    public function setCopyTo(?string $copy_to): void
    {
        $this->copy_to = $copy_to;
    }

    public function getCollection(): ArrayCollection
    {
        $collection = parent::getCollection();
        $this->extendCollectionByFields($collection);
        if ($this->getAnalyzer()) {
            $collection->set('analyzer', $this->getAnalyzer());
        }
        if ($this->isEagerGlobalOrdinals()) {
            $collection->set('eager_global_ordinals', true);
        }

        if (false === $this->isIndex()) {
            $collection->set('index', $this->isIndex());
        }

        if (true === $this->isStore()) {
            $collection->set('store', $this->isStore());
        }

        if ($this->getCopyTo()) {
            $collection->set('copy_to', $this->copy_to);
        }

        $meta = $this->provideMetadataAsArray();
        if ($meta) {
            $collection->set('meta', $meta);
        }

        return $collection;
    }
}
