<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping;

class MappingMetadataProvider implements MetadataProviderInterface
{
    private ?MappingMetadata $metadata = null;

    public function __construct(private readonly MappingMetadataFactory $mappingMetadataFactory)
    {
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getMappingMetadata(): MappingMetadata
    {
        if (null === $this->metadata) {
            $this->metadata = $this->mappingMetadataFactory->create();
        }

        return $this->metadata;
    }
}
