<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Exceptions;

use Exception;
use Throwable;

class MissingKeyResolverException extends Exception
{
    public function __construct(string $key, Throwable $previous = null)
    {
        $message = 'Key Resolver "%s" missing in driver. Please set key resolver by ObjectType/NestedType property keyResolver.';

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
