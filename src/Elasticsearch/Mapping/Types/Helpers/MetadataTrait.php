<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Helpers;

trait MetadataTrait
{
    private Metadata|null $meta = null;

    public function getMeta(): ?Metadata
    {
        return $this->meta;
    }

    public function setMeta(?Metadata $meta): void
    {
        $this->meta = $meta;
    }

    /**
     * @return null|string[]
     */
    protected function provideMetadataAsArray(): ?array
    {
        $meta = $this->getMeta();
        if ($meta) {
            $record = [];
            $unit = $meta->getUnit();
            $unit_type = $meta->getMetricType();

            if ($unit) {
                $record['unit'] = $unit;
            }

            if ($unit_type) {
                $record['unit_type'] = $unit_type;
            }

            return $record;
        }

        return null;
    }
}
