<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Exceptions;

use Throwable;

class MappingJsonCreateException extends \Exception
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Wrong create json index mapping.', 0, $previous);
    }
}
