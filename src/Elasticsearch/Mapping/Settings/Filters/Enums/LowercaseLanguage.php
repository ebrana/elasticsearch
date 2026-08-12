<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters\Enums;

/**
 * Jazyky, pro ktere ma lowercase filtr vlastni implementaci. Ostatni jazyky zadny nepotrebuji.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-lowercase-tokenfilter.html
 */
enum LowercaseLanguage: string
{
    case GREEK = 'greek';
    case IRISH = 'irish';
    case TURKISH = 'turkish';
}
