<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\ObjectsAndRelational;

enum Dynamic: string
{
    case TRUE = 'true';
    case FALSE = 'false';
    case STRICT = 'strict';
}
