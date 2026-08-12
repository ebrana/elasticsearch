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

    /** @var array<string, SuggestEntry[]> */
    private array $suggests = [];

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

        if (isset($record['suggest']) && is_array($record['suggest'])) {
            $this->resolveSuggests($record['suggest']);
        }
    }

    /**
     * @param array<mixed> $suggest
     */
    private function resolveSuggests(array $suggest): void
    {
        foreach ($suggest as $name => $entries) {
            if (!is_array($entries)) {
                continue;
            }

            $resolved = [];
            foreach ($entries as $entry) {
                if (!is_array($entry)) {
                    continue;
                }

                $options = [];
                if (isset($entry['options']) && is_array($entry['options'])) {
                    foreach ($entry['options'] as $option) {
                        if (!is_array($option)) {
                            continue;
                        }

                        /** @var array<string, mixed>|null $source */
                        $source = isset($option['_source']) && is_array($option['_source'])
                            ? $option['_source']
                            : null;

                        $options[] = new SuggestOption(
                            (string)($option['text'] ?? ''),
                            isset($option['score']) ? (float)$option['score'] : null,
                            isset($option['freq']) ? (int)$option['freq'] : null,
                            isset($option['_id']) ? (string)$option['_id'] : null,
                            isset($option['_index']) ? (string)$option['_index'] : null,
                            $source,
                        );
                    }
                }

                $resolved[] = new SuggestEntry(
                    (string)($entry['text'] ?? ''),
                    (int)($entry['offset'] ?? 0),
                    (int)($entry['length'] ?? 0),
                    $options,
                );
            }

            $this->suggests[(string)$name] = $resolved;
        }
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

    /**
     * Navrhy naklicovane podle jmena suggesteru, jak je vrati Elasticsearch.
     *
     * @return array<string, SuggestEntry[]>
     */
    public function getSuggests(): array
    {
        return $this->suggests;
    }

    /**
     * @return SuggestEntry[]
     */
    public function getSuggest(string $name): array
    {
        return $this->suggests[$name] ?? [];
    }
}
