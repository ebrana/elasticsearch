<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Suggest\Enums;

enum SuggestSort: string
{
    case SCORE = 'score';
    case FREQUENCY = 'frequency';
}
