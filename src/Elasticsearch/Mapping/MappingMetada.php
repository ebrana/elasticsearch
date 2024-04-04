<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping;

use Doctrine\Common\Collections\ArrayCollection;

readonly class MappingMetada
{
    /**
     * @param ArrayCollection<string, Index> $metadata
     */
    public function __construct(private ArrayCollection $metadata)
    {
    }

    /**
     * @return ArrayCollection<string, Index>
     */
    public function getMetadata(): ArrayCollection
    {
        return $this->metadata;
    }

    public function getIndexByClasss(string $className): ?Index
    {
        return $this->metadata->get($className);
    }
}
