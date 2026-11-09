<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Exceptions;

use Elasticsearch\Mapping\Types\ObjectsAndRelational\Dynamic;

/**
 * A SourceOnly field sits on a level whose effective `dynamic` is not `false`: Elasticsearch would
 * either map the field dynamically (`true`) or reject the document (`strict`).
 */
class SourceOnlyRequiresDynamicFalseException extends MappingJsonCreateException
{
    public function __construct(string $fieldPath, Dynamic $effectiveDynamic)
    {
        parent::__construct(null, sprintf(
            'SourceOnly field "%s" requires dynamic "false" on its level, effective dynamic is "%s". '
            . 'Set dynamic: Dynamic::FALSE on the Index attribute or on the parent ObjectType.',
            $fieldPath,
            $effectiveDynamic->value
        ));
    }
}
