<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Highlight\Enums;

enum Encoder: string
{
    case DEFAULT = 'default';
    case HTML = 'html';
}
