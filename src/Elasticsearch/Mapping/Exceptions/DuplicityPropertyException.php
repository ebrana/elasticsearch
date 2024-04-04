<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Exceptions;

use Elasticsearch\Mapping\Types\AbstractType;
use Exception;

class DuplicityPropertyException extends Exception
{
    public function __construct(AbstractType $type, string $name)
    {
        parent::__construct(sprintf('Multiply mapping type %s with name "%s"', get_class($type), $name));
    }
}
