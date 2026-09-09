<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations\Composite;

/**
 * A single value source in a composite aggregation. The combination of all sources forms the bucket key.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-composite-aggregation.html
 */
interface CompositeSourceInterface
{
    public function getName(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
