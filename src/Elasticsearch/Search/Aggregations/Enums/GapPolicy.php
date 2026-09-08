<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations\Enums;

/**
 * What to do when a value is missing in the path (an empty bucket or a missing metric).
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-pipeline.html
 */
enum GapPolicy: string
{
    /** bucket se preskoci */
    case SKIP = 'skip';
    /** a zero is substituted for the missing value */
    case INSERT_ZEROS = 'insert_zeros';
    /** the value is left as null */
    case KEEP_VALUES = 'keep_values';
}
