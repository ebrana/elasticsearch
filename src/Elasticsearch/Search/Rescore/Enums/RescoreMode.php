<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Rescore\Enums;

/**
 * How the score of the original query is combined with the score of the rescore query.
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
