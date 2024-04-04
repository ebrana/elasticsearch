<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Helpers\Metadata;
use Elasticsearch\Mapping\Types\Helpers\MetadataTrait;
use Elasticsearch\Mapping\Types\Helpers\OnScriptError;
use Elasticsearch\Mapping\Types\Helpers\OnScriptTrait;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
final class BooleanType extends AbstractType
{
    use MetadataTrait;
    use OnScriptTrait;

    public function __construct(
        private bool $doc_values = false,
        private bool $store = false,
        private bool $index = true,
        private ?string $null_value = null,
        OnScriptError $on_script_error = OnScriptError::FAIL,
        ?string $script = null,
        ?Metadata $meta = null,
        ?string $name = null,
        ?string $context = null,
    ) {
        parent::__construct();

        $this->context = $context;
        $this->type = 'boolean';
        $this->meta = $meta;
        $this->script = $script;
        $this->on_script_error = $on_script_error;
        if ($name && $name !== '') {
            $this->setName($name);
        }
    }

    public function isDocValues(): bool
    {
        return $this->doc_values;
    }

    public function setDocValues(bool $doc_values): void
    {
        $this->doc_values = $doc_values;
    }

    public function isStore(): bool
    {
        return $this->store;
    }

    public function setStore(bool $store): void
    {
        $this->store = $store;
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

    public function getCollection(): ArrayCollection
    {
        $collection = parent::getCollection();

        if (false === $this->index) {
            $collection->set('index', $this->index);
        }

        if ($this->doc_values) {
            $collection->set('doc_values', $this->doc_values);
        }

        if ($this->store) {
            $collection->set('store', $this->store);
        }

        $this->provideOnScriptAsArray($collection);

        if ($this->null_value) {
            $collection->set('null_value', $this->null_value);
        }

        $meta = $this->provideMetadataAsArray();
        if ($meta) {
            $collection->set('meta', $meta);
        }

        return $collection;
    }
}
