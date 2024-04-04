<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Enums;

enum BoolType: string
{
    case MUST = 'must';
    case FILTER = 'filter';
    case SHOULD = 'should';
    case MUST_NOT = 'must_not';
}
