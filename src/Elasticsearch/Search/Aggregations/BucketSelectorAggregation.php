<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithGapPolicy;

/**
 * Vyhodi buckety, pro ktere skript vrati false - obdoba HAVING v SQL. Bezi az nad hotovymi
 * buckety, takze `size` u rodicovske agregace se pocita pred nim.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-pipeline-bucket-selector-aggregation.html
 */
class BucketSelectorAggregation extends AbstractAggregation
{
    use WithGapPolicy;

    /**
     * @param array<string, string> $bucketsPath jmeno promenne ve skriptu => cesta k metrice
     * @param array<string, mixed>|string $script
     */
    public function __construct(
        string $name,
        private readonly array $bucketsPath,
        private readonly array|string $script,
    ) {
        $this->name = $name;
    }

    public function payload(): ArrayCollection
    {
        $parameters = [
            'buckets_path' => $this->bucketsPath,
            'script'       => $this->script,
        ];
        $this->provideGapPolicy($parameters);

        return new ArrayCollection(['bucket_selector' => $parameters]);
    }
}
