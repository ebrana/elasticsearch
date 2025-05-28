<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Exceptions;

use Exception;
use Throwable;

class NotFoundMetadataIndexException extends Exception
{
    public function __construct(string $entityClass, ?Throwable $previous = null)
    {
        parent::__construct('Not found metadata index for entity "' . $entityClass . '".', 0, $previous);
    }
}
