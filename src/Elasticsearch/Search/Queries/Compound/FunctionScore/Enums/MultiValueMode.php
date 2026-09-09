<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore\Enums;

/**
 * Which value to take when the field of a decay function holds several values.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html#function-decay
 */
enum MultiValueMode: string
{
    case MIN = 'min';
    case MAX = 'max';
    case AVG = 'avg';
    case SUM = 'sum';
}
