<?php declare(strict_types=1);

namespace Elasticsearch\Mapping\Exceptions;

use Exception;
use Throwable;

class MissingDefaultKeyResolverException extends Exception
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Default Key Resolver missing in driver. Please set key resolver by setDefaultKeyResolver function.', 0, $previous);
    }
}
