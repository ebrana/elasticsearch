<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common\Keywords\Enums;

enum Similary: string
{
    case BM25 = 'BM25';
    case BOOLEAN = 'boolean';
}
