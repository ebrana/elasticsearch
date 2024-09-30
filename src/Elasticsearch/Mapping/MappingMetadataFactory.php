<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Drivers\DriverInterface;
use Psr\Cache\CacheItemPoolInterface;

final readonly class MappingMetadataFactory
{
    /**
     * @param string[] $classes
     */
    public function __construct(
        private DriverInterface $driver,
        private array $classes,
        private ?CacheItemPoolInterface $cacheItemPool = null,
    ) {
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function create(): MappingMetada
    {
        $mappingMetadata = $this->loadCache();
        if ($mappingMetadata) {
            return $mappingMetadata;
        }

        $metadata = new ArrayCollection();

        foreach ($this->classes as $class) {
            $indexMetadata = $this->driver->loadMetadata($class);
            $metadata->set($class, $indexMetadata);
        }

        $mappingMetadata = new MappingMetada($metadata);

        $this->saveCache($mappingMetadata);

        return $mappingMetadata;
    }

    private function getCacheKey(): string
    {
        return 'es_$' . strtoupper(str_replace('\\', '_', __NAMESPACE__)) . '_METADATA';
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function loadCache(): ?MappingMetada
    {
        if (null === $this->cacheItemPool) {
            return null;
        }

        $mappingMetadata = null;
        $item = $this->cacheItemPool->getItem($this->getCacheKey());
        if ($item->isHit()) {
            /** @var \Elasticsearch\Mapping\MappingMetada|null $mappingMetadata */
            $mappingMetadata = $item->get();
        }

        return $mappingMetadata;
    }

    private function saveCache(MappingMetada $mappingMetadata): void
    {
        if ($this->cacheItemPool) {
            $item = $this->cacheItemPool->getItem($this->getCacheKey());
            $item->set($mappingMetadata);
            $this->cacheItemPool->save($item);
        }
    }
}
