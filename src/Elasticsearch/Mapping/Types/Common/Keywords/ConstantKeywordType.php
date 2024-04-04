<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common\Keywords;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Helpers\MetadataTrait;
use Elasticsearch\Mapping\Types\Helpers\MultiFieldsInterface;
use Elasticsearch\Mapping\Types\Helpers\MultiFieldsTrait;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
final class ConstantKeywordType extends AbstractType implements MultiFieldsInterface
{
    use MetadataTrait;
    use MultiFieldsTrait;

    /**
     * @param AbstractType[]|null $fields
     */
    public function __construct(
        private ?string $value = null,
        ?string $name = null,
        ?array $fields = null,
        ?string $context = null,
    ) {
        parent::__construct();

        $this->type = 'constant_keyword';
        $this->context = $context;

        if ($name && $name !== '') {
            $this->setName($name);
        }

        $this->createFields($fields);
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getCollection(): ArrayCollection
    {
        $collection = parent::getCollection();
        $this->extendCollectionByFields($collection);
        $meta = $this->provideMetadataAsArray();
        if ($meta) {
            $collection->set('meta', $meta);
        }
        if ($this->value) {
            $collection->set('value', $this->value);
        }

        return $collection;
    }
}
