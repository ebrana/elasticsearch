<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Indexing\Exceptions\DocumentToJsonException;
use Elasticsearch\Indexing\Interfaces\DocumentInterface;
use Elasticsearch\Mapping\Index;
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
     * @return array<string, scalar|array<string|int,scalar|null>|null>
     */
    public function toArray(): array
    {
        /** @var array<string, scalar|array<string|int,scalar|null>|null> $result */
        $result = $this->data->toArray();

        return $result;
    }

    /**
     * @throws DocumentToJsonException
     * @throws JsonException
     */
    public function toJson(): string
    {
        try {
            return json_encode(
                $this->toArray(),
                JSON_PRESERVE_ZERO_FRACTION | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            throw new DocumentToJsonException($exception);
        }
    }
}
