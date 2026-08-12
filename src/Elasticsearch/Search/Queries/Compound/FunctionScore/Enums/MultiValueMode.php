<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore\Enums;

/**
 * Kterou hodnotu vzit, kdyz ma pole u decay funkce vic hodnot.
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
