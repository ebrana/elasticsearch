<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping;

interface MetadataProviderInterface
{
    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getMappingMetadata(): MappingMetada;
}
