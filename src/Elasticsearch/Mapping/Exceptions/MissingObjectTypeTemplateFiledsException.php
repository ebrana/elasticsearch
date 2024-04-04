<?php declare(strict_types=1);

namespace Elasticsearch\Mapping\Exceptions;

use Exception;
use Throwable;

class MissingObjectTypeTemplateFiledsException extends Exception
{
    public function __construct(string $class, string $property, Throwable $previous = null)
    {
        parent::__construct(sprintf('Please set fieldsTemplate in property "%s" on class %s', $property, $class), 0, $previous);
    }
}
