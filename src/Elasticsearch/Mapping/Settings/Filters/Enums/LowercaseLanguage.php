<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters\Enums;

/**
 * Languages for which the lowercase filter has its own implementation. Other languages need none.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-lowercase-tokenfilter.html
 */
enum LowercaseLanguage: string
{
    case GREEK = 'greek';
    case IRISH = 'irish';
    case TURKISH = 'turkish';
}
