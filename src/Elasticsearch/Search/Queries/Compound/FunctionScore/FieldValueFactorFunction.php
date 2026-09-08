<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore;

use Elasticsearch\Search\Queries\Compound\FunctionScore\Enums\FieldValueFactorModifier;
use Elasticsearch\Search\Queries\Query;

/**
 * Computes the score from the value of a numeric field. `missing` is the value used when the field
 * is absent - without it such documents end up with an error.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html#function-field-value-factor
 */
readonly class FieldValueFactorFunction extends AbstractScoreFunction
{
    public function __construct(
        private string $field,
        private ?float $factor = null,
        private ?FieldValueFactorModifier $modifier = null,
        private ?float $missing = null,
        ?Query $filter = null,
        ?float $weight = null,
    ) {
        parent::__construct($filter, $weight);
    }

    protected function provideFunction(): array
    {
        $function = ['field' => $this->field];

        if (null !== $this->factor) {
            $function['factor'] = $this->factor;
        }

        if (null !== $this->modifier) {
            $function['modifier'] = $this->modifier->value;
        }

        if (null !== $this->missing) {
            $function['missing'] = $this->missing;
        }

        return ['field_value_factor' => $function];
    }
}
