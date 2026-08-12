<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Rescore\Enums;

/**
 * Jak se spoji skore z puvodni query se skorem z rescore query.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/filter-search-results.html#rescore
 */
enum RescoreMode: string
{
    case TOTAL = 'total';
    case MULTIPLY = 'multiply';
    case AVG = 'avg';
    case MAX = 'max';
    case MIN = 'min';
}
