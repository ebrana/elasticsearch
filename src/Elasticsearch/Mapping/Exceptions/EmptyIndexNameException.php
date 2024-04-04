<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Exceptions;

use Throwable;

class EmptyIndexNameException extends \Exception
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Empty index name.', 0, $previous);
    }
}
