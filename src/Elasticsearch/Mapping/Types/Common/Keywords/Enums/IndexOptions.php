<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common\Keywords\Enums;

enum IndexOptions: string
{
    case DOCS = 'docs';
    case FREQS = 'freqs';
    case POSITIONS = 'positions';
    case OFFSETS = 'offsets';
}
