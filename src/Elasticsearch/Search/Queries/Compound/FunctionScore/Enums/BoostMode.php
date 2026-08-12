<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore\Enums;

/**
 * Jak se vysledek funkci spoji se skorem z puvodni query.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html
 */
enum BoostMode: string
{
    case MULTIPLY = 'multiply';
    case REPLACE = 'replace';
    case SUM = 'sum';
    case AVG = 'avg';
    case MAX = 'max';
    case MIN = 'min';
}
