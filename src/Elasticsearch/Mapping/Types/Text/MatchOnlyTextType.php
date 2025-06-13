<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Text;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Helpers\Metadata;
use Elasticsearch\Mapping\Types\Helpers\MetadataTrait;
use Elasticsearch\Mapping\Types\Helpers\MultiFieldsTrait;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
class MatchOnlyTextType extends AbstractType
{
    use MultiFieldsTrait;
    use MetadataTrait;

    /**
     * @param AbstractType[]|null $fields
     */
    public function __construct(
        private ?string $copy_to = null,
        ?string $name = null,
        ?array $fields = null,
        ?Metadata $meta = null,
    ) {
        parent::__construct();

        $this->type = 'match_only_text';
        $this->meta = $meta;

        if (null !== $name && $name !== '') {
            $this->setName($name);
        }

        $this->createFields($fields);
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

        if ($this->copy_to) {
            $collection->set('copy_to', $this->copy_to);
        }

        $meta = $this->provideMetadataAsArray();
        if ($meta) {
            $collection->set('meta', $meta);
        }

        return $collection;
    }
}
