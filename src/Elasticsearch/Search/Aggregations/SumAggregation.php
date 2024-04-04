<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithMissing;

class SumAggregation extends AbstractAggregation
{
    use WithMissing;

    public function __construct(
        string $name,
        private readonly string $field
    ) {
        $this->name = $name;
    }

    public function payload(): ArrayCollection
    {
        $parameters = [
            'field' => $this->field,
        ];

        if ($this->missing) {
            $parameters['missing'] = $this->missing;
        }

        return new ArrayCollection(['sum' => $parameters]);
    }
}
