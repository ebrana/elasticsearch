<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations\Composite;

abstract class AbstractCompositeSource implements CompositeSourceInterface
{
    protected ?string $order = null;
    protected ?bool $missingBucket = null;

    public function __construct(
        private readonly string $name,
        protected readonly string $field,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * "asc" nebo "desc".
     */
    public function order(string $order): static
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Includes documents that are missing the field as well (otherwise they drop out of the composite).
     */
    public function missingBucket(bool $missingBucket): static
    {
        $this->missingBucket = $missingBucket;

        return $this;
    }

    /**
     * The source-specific part, without the shared options.
     *
     * @return array<string, mixed>
     */
    abstract protected function provideSource(): array;

    /**
     * The source type, e.g. "terms".
     */
    abstract protected function getType(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $source = array_merge(['field' => $this->field], $this->provideSource());

        if (null !== $this->order) {
            $source['order'] = $this->order;
        }

        if (null !== $this->missingBucket) {
            $source['missing_bucket'] = $this->missingBucket;
        }

        return [$this->getName() => [$this->getType() => $source]];
    }
}
