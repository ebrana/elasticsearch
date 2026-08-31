<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations\Enums;

/**
 * Co delat, kdyz v ceste chybi hodnota (prazdny bucket nebo chybejici metrika).
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-pipeline.html
 */
enum GapPolicy: string
{
    /** bucket se preskoci */
    case SKIP = 'skip';
    /** misto chybejici hodnoty se dosadi nula */
    case INSERT_ZEROS = 'insert_zeros';
    /** hodnota se ponecha jako null */
    case KEEP_VALUES = 'keep_values';
}
