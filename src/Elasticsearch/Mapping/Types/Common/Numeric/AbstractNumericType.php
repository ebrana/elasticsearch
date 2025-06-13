<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common\Numeric;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Helpers\Metadata;
use Elasticsearch\Mapping\Types\Helpers\MetadataTrait;

/**
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/number.html
 */
abstract class AbstractNumericType extends AbstractType
{
    use MetadataTrait;

    protected const TYPE = '';

    public function __construct(
        private bool $coerce = false,
        private bool $doc_values = true,
        private bool $ignored_malformed = false,
        private bool $index = true,
        private bool $store = false,
        private ?string $null_value = null,
        ?Metadata $meta = null,
        ?string $name = null,
        ?string $context = null,
    ) {
        parent::__construct();

        $this->context = $context;
        $this->type = static::TYPE;
        $this->meta = $meta;

        if (null !== $name && $name !== '') {
            $this->setName($name);
        }
    }

    public function isCoerce(): bool
    {
        return $this->coerce;
    }

    public function setCoerce(bool $coerce): void
    {
        $this->coerce = $coerce;
    }

    public function isDocValues(): bool
    {
        return $this->doc_values;
    }

    public function setDocValues(bool $doc_values): void
    {
        $this->doc_values = $doc_values;
    }

    public function isIgnoredMalformed(): bool
    {
        return $this->ignored_malformed;
    }

    public function setIgnoredMalformed(bool $ignored_malformed): void
    {
        $this->ignored_malformed = $ignored_malformed;
    }

    public function isIndex(): bool
    {
        return $this->index;
    }

    public function setIndex(bool $index): void
    {
        $this->index = $index;
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

    public function getCollection(): ArrayCollection
    {
        $collection = parent::getCollection();

        $meta = $this->provideMetadataAsArray();
        if ($meta) {
            $collection->set('meta', $meta);
        }

        if ($this->coerce) {
            $collection->set('coerce', $this->coerce);
        }

        if (false === $this->doc_values) {
            $collection->set('doc_values', $this->doc_values);
        }

        if ($this->ignored_malformed) {
            $collection->set('ignored_malformed', $this->ignored_malformed);
        }

        if (false === $this->index) {
            $collection->set('index', $this->index);
        }

        if ($this->store) {
            $collection->set('store', $this->store);
        }

        if ($this->null_value) {
            $collection->set('null_value', $this->null_value);
        }

        return $collection;
    }

}
