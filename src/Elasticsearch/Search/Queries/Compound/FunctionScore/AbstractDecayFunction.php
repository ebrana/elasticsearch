<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore;

use Elasticsearch\Search\Queries\Compound\FunctionScore\Enums\MultiValueMode;
use Elasticsearch\Search\Queries\Query;

/**
 * Zaklad pro gauss, exp a linear - skore klesa se vzdalenosti od `origin`.
 * Ve `scale` je vzdalenost, na ktere skore spadne na `decay` (default 0.5).
 * Pole musi byt numeric, date nebo geo_point.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html#function-decay
 */
abstract readonly class AbstractDecayFunction extends AbstractScoreFunction
{
    /**
     * @param string|int|float|array<int|string, mixed> $origin
     * @param string|int|float $scale
     * @param string|int|float|null $offset
     */
    public function __construct(
        private string $type,
        private string $field,
        private string|int|float|array $origin,
        private string|int|float $scale,
        private string|int|float|null $offset = null,
        private ?float $decay = null,
        private ?MultiValueMode $multi_value_mode = null,
        ?Query $filter = null,
        ?float $weight = null,
    ) {
        parent::__construct($filter, $weight);
    }

    public function getField(): string
    {
        return $this->field;
    }

    protected function provideFunction(): array
    {
        $parameters = [
            'origin' => $this->origin,
            'scale'  => $this->scale,
        ];

        if (null !== $this->offset) {
            $parameters['offset'] = $this->offset;
        }

        if (null !== $this->decay) {
            $parameters['decay'] = $this->decay;
        }

        $function = [$this->field => $parameters];

        // multi_value_mode je sourozenec pole, ne jeho soucast
        if (null !== $this->multi_value_mode) {
            $function['multi_value_mode'] = $this->multi_value_mode->value;
        }

        return [$this->type => $function];
    }
}
