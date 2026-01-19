<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithMissing;
use InvalidArgumentException;

class CardinalityAggregation extends AbstractAggregation
{
    use WithMissing;

    public function __construct(
        string $name,
        private readonly string $field,
        private readonly ?int $precision_threshold = null
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

        if (null !== $this->precision_threshold) {
            if ($this->precision_threshold <= 0) {
                throw new InvalidArgumentException('Precision threshold must be greater than 0.');
            }
            if ($this->precision_threshold > 40000) {
                throw new InvalidArgumentException('Precision threshold must be less than 40000.');
            }
            $parameters['precision_threshold'] = $this->precision_threshold;
        }

        return new ArrayCollection(['cardinality' => $parameters]);
    }
}
