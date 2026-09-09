<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore\Enums;

/**
 * How the scores of the individual functions are combined with each other.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html
 */
enum ScoreMode: string
{
    case MULTIPLY = 'multiply';
    case SUM = 'sum';
    case AVG = 'avg';
    case FIRST = 'first';
    case MAX = 'max';
    case MIN = 'min';
}
