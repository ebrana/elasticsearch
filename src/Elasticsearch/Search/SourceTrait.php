<?php

declare(strict_types=1);

namespace Elasticsearch\Search;

trait SourceTrait
{
    /**
     * @var string[]|string|bool|null
     */
    private array|string|bool|null $source = null;

    /**
     * @return bool|string[]|string|null
     */
    public function getSource(): bool|array|string|null
    {
        return $this->source;
    }

    /**
     * @param bool|string[]|string|null|array<string, array<int, string>> $source
     * @return void
     */
    public function setSource(bool|array|string|null $source): void
    {
        $this->source = $source;
    }
}
