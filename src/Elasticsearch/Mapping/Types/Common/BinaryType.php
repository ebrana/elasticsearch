<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
final class BinaryType extends AbstractType
{
    public function __construct(
        private readonly bool $doc_values = false,
        private readonly bool $store = false,
        ?string $name = null,
        ?string $context = null,
    )
    {
        parent::__construct();

        $this->type = 'binary';
        $this->context = $context;
        if ($name && $name !== '') {
            $this->setName($name);
        }
    }

    public function isDocValues(): bool
    {
        return $this->doc_values;
    }

    public function isStore(): bool
    {
        return $this->store;
    }

    public function getCollection(): ArrayCollection
    {
        $collection = parent::getCollection();

        $collection->set('type', $this->getType());

        if ($this->doc_values) {
            $collection->set('doc_values', $this->doc_values);
        }

        if ($this->store) {
            $collection->set('store', $this->store);
        }

        return $collection;
    }
}
