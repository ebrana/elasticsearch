<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore\Enums;

/**
 * Matematicka uprava hodnoty pole pred pouzitim ve skore.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html#function-field-value-factor
 */
enum FieldValueFactorModifier: string
{
    case NONE = 'none';
    case LOG = 'log';
    case LOG1P = 'log1p';
    case LOG2P = 'log2p';
    case LN = 'ln';
    case LN1P = 'ln1p';
    case LN2P = 'ln2p';
    case SQUARE = 'square';
    case SQRT = 'sqrt';
    case RECIPROCAL = 'reciprocal';
}
