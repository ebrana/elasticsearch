<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Exceptions;

use Exception;
use Throwable;

class IndexDefinitionNotFoundException extends Exception
{
    public function __construct(string $class, Throwable $previous = null)
    {
        parent::__construct(sprintf('Missing index definition: %s', $class), 0, $previous);
    }
}
