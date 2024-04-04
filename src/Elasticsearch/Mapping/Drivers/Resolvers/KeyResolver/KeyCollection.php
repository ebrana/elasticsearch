<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Resolvers\KeyResolver;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

final class KeyCollection implements IteratorAggregate
{
    /**
     * @var string[]
     */
    private array $keys = [];

    public function add(string $key): void
    {
        $this->keys[] = $key;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->keys);
    }
}
