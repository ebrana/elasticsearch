<?php

declare(strict_types=1);

namespace Elasticsearch\Search;

/**
 * Point in Time - a frozen view of the index, so that deep paging via search_after returns
 * consistent results even while indexing goes on.
 *
 * It is opened through Connection::openPointInTime() and once paging is done it has to be closed
 * through Connection::closePointInTime(), otherwise it holds resources until keep_alive expires.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/point-in-time-api.html
 */
final readonly class PointInTime
{
    public function __construct(
        private string $id,
        private ?string $keep_alive = null,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getKeepAlive(): ?string
    {
        return $this->keep_alive;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $data = ['id' => $this->id];

        if (null !== $this->keep_alive) {
            $data['keep_alive'] = $this->keep_alive;
        }

        return $data;
    }
}
