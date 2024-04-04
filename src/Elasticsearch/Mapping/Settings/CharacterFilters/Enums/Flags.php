<?php

namespace Elasticsearch\Mapping\Settings\CharacterFilters\Enums;

/**
 * @see https://docs.oracle.com/javase/8/docs/api/java/util/regex/Pattern.html#field.summary
 */
enum Flags: string
{
    case CASE_INSENSITIVE = 'CASE_INSENSITIVE';
    case COMMENTS = 'COMMENTS';
    case CANON_EQ = 'CANON_EQ';
    case DOTALL = 'DOTALL';
    case LITERAL = 'LITERAL';
    case MULTILINE = 'MULTILINE';
    case UNICODE_CASE = 'UNICODE_CASE';
    case UNICODE_CHARACTER_CLASS = 'UNICODE_CHARACTER_CLASS';
    case UNIX_LINES = 'UNIX_LINES';
}
