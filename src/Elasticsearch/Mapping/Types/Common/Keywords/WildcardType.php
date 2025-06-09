<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common\Keywords;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Helpers\MultiFieldsInterface;
use Elasticsearch\Mapping\Types\Helpers\MultiFieldsTrait;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
final class WildcardType extends AbstractType implements MultiFieldsInterface
{
    use MultiFieldsTrait;

    /**
     * @param AbstractType[]|null $fields
     */
    public function __construct(
        private int $ignore_above = 2147483647,
        private ?string $null_value = null,
        ?array $fields = null,
        ?string $name = null,
        ?string $context = null,
    ) {
        parent::__construct();

        $this->type = 'wildcard';
        $this->context = $context;

        if (null !== $name && $name !== '') {
            $this->setName($name);
        }

        $this->createFields($fields);
    }

    public function getNullValue(): ?string
    {
        return $this->null_value;
    }

    public function setNullValue(?string $null_value): void
    {
        $this->null_value = $null_value;
    }

    public function getIgnoreAbove(): int
    {
        return $this->ignore_above;
    }

    public function setIgnoreAbove(int $ignore_above): void
    {
        $this->ignore_above = $ignore_above;
    }

    public function getCollection(): ArrayCollection
    {
        $collection = parent::getCollection();
        $this->extendCollectionByFields($collection);

        if ($this->ignore_above !== 2147483647) {
            $collection->set('ignore_above', $this->ignore_above);
        }

        if ($this->null_value) {
            $collection->set('null_value', $this->null_value);
        }

        return $collection;
    }
}
