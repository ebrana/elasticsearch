<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Exceptions;

use Exception;
use Throwable;

class MissingPostEventException extends Exception
{
    public function __construct(string $key, ?Throwable $previous = null)
    {
        $message = 'PostEvent "%s" missing in driver. Please set postEventClass.';

        parent::__construct(
            sprintf(
                $message,
                $key
            ),
            0,
            $previous
        );
    }
}
