<?php declare(strict_types=1);

namespace Elasticsearch\Indexing\Exceptions;

use Exception;
use Throwable;

class DocumentToJsonException extends Exception
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Create json from document error.', 0, $previous);
    }
}
