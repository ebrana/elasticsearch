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
     * Zaradi i dokumenty, kterym pole chybi (jinak z composite vypadnou).
     */
    public function missingBucket(bool $missingBucket): static
    {
        $this->missingBucket = $missingBucket;

        return $this;
    }

    /**
     * Vlastni cast zdroje bez spolecnych voleb.
     *
     * @return array<string, mixed>
     */
    abstract protected function provideSource(): array;

    /**
     * Typ zdroje, napr. "terms".
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
