<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Params;

enum OpType: string
{
    case INDEX = 'index';
    case CREATE = 'create';

    public function toString(): string {
        return $this->value;
    }
}
