<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Drivers\DriverInterface;

final readonly class MappingMetadataFactory
{
    /**
     * @param string[] $classes
     */
    public function __construct(
        private DriverInterface $driver,
        private array $classes)
    {
    }

    public function create(): MappingMetada
    {
        $metadata = new ArrayCollection();

        foreach ($this->classes as $class) {
            $indexMetadata = $this->driver->loadMetadata($class);
            $metadata->set($class, $indexMetadata);
        }

        return new MappingMetada($metadata);
    }
}
