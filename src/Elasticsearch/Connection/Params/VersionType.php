<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Params;

enum VersionType: string
{
    case EXTERNAL = 'external';
    case EXTERNAL_GTE = 'external_gte';

    public function toString(): string {
        return $this->value;
    }
}
