<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common\Numeric;

use Attribute;
use Elasticsearch\Mapping\Types\Helpers\Metadata;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class ScaledFloatType extends AbstractNumericType
{
    protected const TYPE = 'scaled_float';

    public function __construct(
        protected float $scaling_factor,
        bool $coerce = false,
        bool $doc_values = true,
        bool $ignored_malformed = false,
        bool $index = true,
        bool $store = false,
        ?string $null_value = null,
        ?Metadata $meta = null,
        ?string $name = null,
        ?string $context = null,
    ) {
        parent::__construct($coerce, $doc_values, $ignored_malformed, $index, $store, $null_value, $meta, $name, $context);
    }

    public function getScalingFactor(): float
    {
        return $this->scaling_factor;
    }

    public function setScalingFactor(float $scaling_factor): void
    {
        $this->scaling_factor = $scaling_factor;
    }
}
