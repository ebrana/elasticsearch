<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Specialized;

use Elasticsearch\Search\Queries\Query;

/**
 * Raises the score by the proximity to a given point - for dates (newer first) or for
 * geo_point (a closer store first). The field must be date, date_nanos or geo_point.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-distance-feature-query.html
 */
readonly class DistanceFeatureQuery implements Query
{
    /**
     * @param string|float[] $origin the point measured from (e.g. "now", "2026-01-01" or [lon, lat])
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
