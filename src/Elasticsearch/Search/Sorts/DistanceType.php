<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Sorts;

enum DistanceType: string
{
    case ARC = 'arc';
    case PLANE = 'plane';
}
