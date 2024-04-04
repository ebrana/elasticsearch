<?php

namespace Elasticsearch\Search\Queries\Enums;

enum MultiMatchType: string
{
    case BEST_FIELDS = 'best_fields';
    case MOST_FIELDS = 'most_fields';
    case CROSS_FIELDS = 'cross_fields';
    case PHRASE = 'phrase';
    case PHRASE_PREFIX = 'phrase_prefix';
    case BOOL_PREFIX = 'bool_prefix';
}
