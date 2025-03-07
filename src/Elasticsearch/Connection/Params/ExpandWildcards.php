<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Params;

enum ExpandWildcards: string
{
    case ALL = 'all';
    case OPEN = 'open';
    case CLOSED = 'closed';
    case HIDDEN = 'hidden';
    case NONE = 'none';
    case HIDDEN_OPEN = 'hidden,open';
    case HIDDEN_CLOSED = 'hidden,closed';
    case HIDDEN_BOTH = 'hidden,closed,open';

    public function toString(): string {
        return $this->value;
    }
}
