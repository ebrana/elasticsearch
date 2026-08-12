<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Specialized;

use Elasticsearch\Search\Queries\Query;

/**
 * Zvysuje skore podle blizkosti k zadanemu bodu - u datumu (novinky driv) nebo
 * geo_point (blizsi prodejna driv). Pole musi byt date, date_nanos nebo geo_point.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-distance-feature-query.html
 */
readonly class DistanceFeatureQuery implements Query
{
    /**
     * @param string|float[] $origin bod, od ktereho se meri (napr. "now", "2026-01-01" nebo [lon, lat])
     */
    public function __construct(
        private string $field,
        private string|array $origin,
        private string $pivot,
        private ?float $boost = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'field'  => $this->field,
            'origin' => $this->origin,
            'pivot'  => $this->pivot,
        ];

        if (null !== $this->boost) {
            $data['boost'] = $this->boost;
        }

        return ['distance_feature' => $data];
    }
}
