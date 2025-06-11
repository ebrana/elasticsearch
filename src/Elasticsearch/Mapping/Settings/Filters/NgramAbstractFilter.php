<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractFilter;

/**
 * @deprecated use NgramFilter instead
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class NgramAbstractFilter extends AbstractFilter
{
    public function __construct(
        string $name,
        private readonly int $min_gram = 1,
        private readonly int $max_gram = 2,
        private readonly bool $preserve_original = false
    ) {
        parent::__construct($name, 'ngram');
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

    /**
     * @return array<string, array<string>|int|string|true>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

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
