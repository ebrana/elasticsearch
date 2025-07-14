<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Enums;

enum Operator: string
{
    case OR = 'OR';
    case AND = 'AND';
}
