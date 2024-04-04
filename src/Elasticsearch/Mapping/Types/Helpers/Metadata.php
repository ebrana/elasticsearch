<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Helpers;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
final class Metadata
{
    public function __construct(
        private ?string $unit = null,
        private ?string $metric_type = null)
    {
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): void
    {
        $this->unit = $unit;
    }

    public function getMetricType(): ?string
    {
        return $this->metric_type;
    }

    public function setMetricType(?string $metric_type): void
    {
        $this->metric_type = $metric_type;
    }
}
