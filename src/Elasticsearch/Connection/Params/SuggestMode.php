<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Params;

enum SuggestMode: string
{
    case ALWAYS = 'always';
    case MISSING = 'missing';
    case POPULAR = 'popular';

    public function toString(): string {
        return $this->value;
    }
}
