<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Suggest\Enums;

/**
 * Pro ktere termy se maji navrhy hledat.
 *
 * Stejne hodnoty ma i Connection\Params\SuggestMode, ktery patri ke query parametrum
 * _search a ma navic toString() pro jejich serializaci. Tenhle je pro telo requestu.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters.html#term-suggester
 */
enum SuggestMode: string
{
    /** jen pro termy, ktere v indexu nejsou */
    case MISSING = 'missing';
    /** jen pro termy castejsi nez zadany */
    case POPULAR = 'popular';
    case ALWAYS = 'always';
}
