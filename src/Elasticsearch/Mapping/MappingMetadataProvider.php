<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping;

class MappingMetadataProvider implements MetadataProviderInterface
{
    private ?MappingMetada $metadata = null;

    public function __construct(private readonly MappingMetadataFactory $mappingMetadataFactory)
    {
    }

    public function getMappingMetadata(): MappingMetada
    {
        if (null === $this->metadata) {
            $this->metadata = $this->mappingMetadataFactory->create();
        }

        return $this->metadata;
    }
}
