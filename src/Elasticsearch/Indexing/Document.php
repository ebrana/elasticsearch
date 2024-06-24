<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Indexing\Exceptions\DocumentToJsonException;
use Elasticsearch\Indexing\Interfaces\DocumentInterface;
use Elasticsearch\Mapping\Index;
use Generator;
use JsonException;

final class Document implements DocumentInterface
{
    private ArrayCollection $data;

    public function __construct(
        private readonly Index $index,
        private readonly ?string $id = null
    ) {
        $this->data = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param mixed $value scalar|array<string|int,scalar|null>|null accepted
     * @throws DocumentToJsonException
     */
    public function set(string $key, mixed $value): void
    {
        if ($value !== null && !is_scalar($value) && !is_array($value)) {
            throw new DocumentToJsonException();
        }
        $this->data->set($key, $value);
    }

    public function getIndex(): Index
    {
        return $this->index;
    }

    /**
     * @return Generator
     */
    public function toArray(): Generator
    {
        /**
         * @var string               $key
         * @var scalar|string[]|null $item
         */
        foreach ($this->data as $key => $item) {
            yield $key => $item;
        }
    }

    /**
     * @throws DocumentToJsonException
     * @throws JsonException
     */
    public function toJson(): string
    {
        $json = json_encode(iterator_to_array($this->toArray()), JSON_THROW_ON_ERROR);

        if (empty($json)) {
            throw new DocumentToJsonException();
        }

        return $json;
    }
}
