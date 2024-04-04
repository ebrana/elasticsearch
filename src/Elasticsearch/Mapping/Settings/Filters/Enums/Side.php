<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters\Enums;

enum Side: string
{
    case FRONT = 'front';
    case BACK = 'back';
}
