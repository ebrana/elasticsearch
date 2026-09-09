<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Suggest\Enums;

/**
 * Algoritmus pro porovnavani podobnosti termu.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters.html#term-suggester
 */
enum StringDistance: string
{
    case INTERNAL = 'internal';
    case DAMERAU_LEVENSHTEIN = 'damerau_levenshtein';
    case LEVENSHTEIN = 'levenshtein';
    case JARO_WINKLER = 'jaro_winkler';
    case NGRAM = 'ngram';
}
