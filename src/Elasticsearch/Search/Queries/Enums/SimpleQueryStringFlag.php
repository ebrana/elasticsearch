<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Enums;

/**
 * Which operators the user may use in a simple_query_string. In the resulting query
 * they are joined into a pipe-separated string.
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
