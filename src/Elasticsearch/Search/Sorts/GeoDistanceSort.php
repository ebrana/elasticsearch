<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Sorts;

use Generator;

class GeoDistanceSort implements SortInterface
{
    private const string FIELD = '_geo_distance';

    /**
     * @param int[]|array<int[]>|string $pinLocation
     */
    public function __construct(
        private readonly array|string $pinLocation,
        private readonly DistanceType $distance_type = DistanceType::ARC,
        private readonly string $unit = 'm',
        private readonly ?SortDirection $order = null,
        private readonly ?SortMode $mode = null,
        private readonly bool $ignore_unmapped = false,
    ) {
    }

    public function toArray(): Generator
    {
        $payload = [];

        if ($this->order) {
            $payload['order'] = $this->order->value;
        }

        if ($this->mode) {
            $payload['mode'] = $this->mode->value;
        }

        $payload['pin.location'] = $this->pinLocation;
        $payload['distance_type'] = $this->distance_type->value;
        $payload['unit'] = $this->unit;
        $payload['ignore_unmapped'] = $this->ignore_unmapped;

        yield self::FIELD => $payload;
    }

}
