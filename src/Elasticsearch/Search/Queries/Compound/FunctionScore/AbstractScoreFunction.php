<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore;

use Elasticsearch\Search\Queries\Query;

/**
 * `filter` (the function applies only to its matches) and `weight` (a multiplier of the function
 * score) can be used by any function, hence they live here.
 */
abstract readonly class AbstractScoreFunction implements ScoreFunctionInterface
{
    public function __construct(
        private ?Query $filter = null,
        private ?float $weight = null,
    ) {
    }

    public function getFilter(): ?Query
    {
        return $this->filter;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    /**
     * The function-specific part, i.e. e.g. ['field_value_factor' => [...]].
     *
     * @return array<string, mixed>
     */
    abstract protected function provideFunction(): array;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = $this->provideFunction();

        if (null !== $this->filter) {
            $data['filter'] = $this->filter->toArray();
        }

        if (null !== $this->weight) {
            $data['weight'] = $this->weight;
        }

        return $data;
    }
}
