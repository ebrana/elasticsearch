<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Results;

use Doctrine\Common\Collections\ArrayCollection;

final class Result
{
    private ?int $took = null;
    private bool $timedOut = false;
    private Shards $shards;
    private HitsCollection $hits;
    private ArrayCollection $aggregations;

    /** @phpstan-ignore-next-line */
    public function __construct(array $record)
    {
        if (isset($record['took'])) {
            $this->took = (int)$record['took'];
        }

        if (isset($record['timed_out'])) {
            $this->timedOut = (bool)$record['timed_out'];
        }

        if (isset($record['_shards'])) {
            $this->shards = new Shards(
                $record['_shards']['total'],
                $record['_shards']['successful'],
                $record['_shards']['skipped'],
                $record['_shards']['failed']
            );
        }

        if (isset($record['hits'])) {
            $this->hits = new HitsCollection();
            if (isset($record['hits']['max_score'])) {
                $this->hits->setMaxScore((float)$record['hits']['max_score']);
            }
            if (isset($record['hits']['total']['value'])) {
                $this->hits->setTotalValue($record['hits']['total']['value']);
            }
            if (isset($record['hits']['total']['relation'])) {
                $this->hits->setTotalRelation($record['hits']['total']['relation']);
            }

            if (isset($record['hits']['hits'])) {
                $this->hits->setCollection(new ArrayCollection($record['hits']['hits']));
            }
        }

        $this->aggregations = new ArrayCollection($record['aggregations'] ?? []);
    }

    public function getTook(): ?int
    {
        return $this->took;
    }

    public function isTimedOut(): bool
    {
        return $this->timedOut;
    }

    public function getShards(): Shards
    {
        return $this->shards;
    }

    public function getHits(): HitsCollection
    {
        return $this->hits;
    }

    public function getAggregations(): ArrayCollection
    {
        return $this->aggregations;
    }
}
