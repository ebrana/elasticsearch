<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations\Composite;

/**
 * Jeden zdroj hodnot v composite agregaci. Kombinace vsech zdroju tvori klic bucketu.
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
