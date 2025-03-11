<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Params;

enum Operator: string
{
    case AND = 'AND';
    case OR = 'OR';

    public function toString(): string {
        return $this->value;
    }
}
