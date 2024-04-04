<?php declare(strict_types=1);

namespace Elasticsearch\Mapping\Exceptions;

use Exception;
use Throwable;

class MissingKeyResolverException extends Exception
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Key Resolver missing in driver. Please set key resolver by setKeyResolver function.', 0, $previous);
    }
}
