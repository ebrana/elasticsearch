<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Exceptions;

use RuntimeException;

class MissingMappingMetadataIndexException extends RuntimeException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Index by name "%s" not found.', $name));
    }
}
