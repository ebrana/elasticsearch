<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Exceptions;

use Throwable;

class MappingJsonCreateException extends \Exception
{
    public function __construct(?Throwable $previous = null, string $message = 'Wrong create json index mapping.')
    {
        parent::__construct($message, 0, $previous);
    }
}
