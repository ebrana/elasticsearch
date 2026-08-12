<?php

declare(strict_types=1);

namespace Elasticsearch\Search;

/**
 * Point in Time - zamrznuty pohled na index, aby hluboke strankovani pres search_after
 * vracelo konzistentni vysledky i kdyz se mezitim indexuje.
 *
 * Otevira se pres Connection::openPointInTime() a po dostrankovani je potreba ho zavrit
 * pres Connection::closePointInTime(), jinak drzi zdroje az do vyprseni keep_alive.
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
