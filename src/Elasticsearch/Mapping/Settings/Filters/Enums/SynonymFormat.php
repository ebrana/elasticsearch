<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters\Enums;

enum SynonymFormat: string
{
    case SOLR = 'solr';
    case WORDNET = 'wordnet';
}
