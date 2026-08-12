<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Enums;

/**
 * Ktere operatory smi uzivatel v simple_query_string pouzit. Ve vysledne query
 * se spojuji do retezce oddeleneho svislitkem.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-simple-query-string-query.html
 */
enum SimpleQueryStringFlag: string
{
    case ALL = 'ALL';
    case AND = 'AND';
    case ESCAPE = 'ESCAPE';
    case FUZZY = 'FUZZY';
    case NEAR = 'NEAR';
    case NONE = 'NONE';
    case NOT = 'NOT';
    case OR = 'OR';
    case PHRASE = 'PHRASE';
    case PRECEDENCE = 'PRECEDENCE';
    case PREFIX = 'PREFIX';
    case SLOP = 'SLOP';
    case WHITESPACE = 'WHITESPACE';
}
