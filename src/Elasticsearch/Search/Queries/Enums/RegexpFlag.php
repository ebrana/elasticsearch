<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Enums;

/**
 * Volitelne konstrukce v regexp query. Ve vysledne query se spojuji do retezce
 * oddeleneho svislitkem.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/regexp-syntax.html
 */
enum RegexpFlag: string
{
    case ALL = 'ALL';
    case ANYSTRING = 'ANYSTRING';
    case COMPLEMENT = 'COMPLEMENT';
    case EMPTY = 'EMPTY';
    case INTERSECTION = 'INTERSECTION';
    case INTERVAL = 'INTERVAL';
    case NONE = 'NONE';
}
