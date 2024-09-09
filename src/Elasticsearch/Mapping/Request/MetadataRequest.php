<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Request;

use Elasticsearch\Mapping\Index;

class MetadataRequest
{
    public function __construct(
        private readonly Index $index,
        private string $mappingJson = ''
    ) {
    }

    public function getIndex(): Index
    {
        return $this->index;
    }

    public function getMappingJson(): string
    {
        return $this->mappingJson;
    }

    public function setMappingJson(string $mappingJson): void
    {
        $this->mappingJson = $mappingJson;
    }
}
