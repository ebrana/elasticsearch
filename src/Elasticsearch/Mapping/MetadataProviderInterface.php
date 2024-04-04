<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping;

interface MetadataProviderInterface
{
    public function getMappingMetadata(): MappingMetada;
}