<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations\Concerns;

trait WithMissing
{
    protected ?string $missing = null;

    public function missing(string $missingValue): self
    {
        $this->missing = $missingValue;

        return $this;
    }
}
