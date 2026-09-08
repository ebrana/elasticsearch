<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations\Concerns;

use Elasticsearch\Search\Aggregations\Enums\GapPolicy;

trait WithGapPolicy
{
    protected ?GapPolicy $gapPolicy = null;
    protected ?string $format = null;

    public function gapPolicy(GapPolicy $gapPolicy): self
    {
        $this->gapPolicy = $gapPolicy;

        return $this;
    }

    public function format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function provideGapPolicy(array &$parameters): void
    {
        if (null !== $this->gapPolicy) {
            $parameters['gap_policy'] = $this->gapPolicy->value;
        }

        if (null !== $this->format) {
            $parameters['format'] = $this->format;
        }
    }
}
