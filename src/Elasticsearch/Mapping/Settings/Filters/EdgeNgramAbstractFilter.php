<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractFilter;
use Elasticsearch\Mapping\Settings\Filters\Enums\Side;

/**
 * @deprecated use EdgeNgramFilter instead
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class EdgeNgramAbstractFilter extends AbstractFilter
{
    public function __construct(
        string $name,
        private readonly int $min_gram = 1,
        private readonly int $max_gram = 2,
        private readonly bool $preserve_original = false,
        private readonly Side $side = Side::FRONT
    ) {
        parent::__construct($name, 'edge_ngram');
    }

    public function getMinGram(): int
    {
        return $this->min_gram;
    }

    public function getMaxGram(): int
    {
        return $this->max_gram;
    }

    public function isPreserveOriginal(): bool
    {
        return $this->preserve_original;
    }

    public function getSide(): Side
    {
        return $this->side;
    }

    /**
     * @return array<string, array<string>|int|string|true>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        $data['side'] = $this->getSide()->value;

        if ($this->getMinGram() !== 1) {
            $data['min_gram'] = $this->getMinGram();
        }

        if ($this->getMaxGram() !== 2) {
            $data['max_gram'] = $this->getMaxGram();
        }

        if ($this->isPreserveOriginal()) {
            $data['preserve_original'] = $this->isPreserveOriginal();
        }

        return $data;
    }
}
