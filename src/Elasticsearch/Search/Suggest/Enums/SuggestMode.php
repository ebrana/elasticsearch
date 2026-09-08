<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Suggest\Enums;

/**
 * For which terms suggestions should be looked up. It is used both in the request body (TermSuggest,
 * DirectGenerator) and as the suggest_mode query parameter in SearchParams.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters.html#term-suggester
 */
enum SuggestMode: string
{
    /** only for terms that are not in the index */
    case MISSING = 'missing';
    /** only for terms more frequent than the given one */
    case POPULAR = 'popular';
    case ALWAYS = 'always';
}
