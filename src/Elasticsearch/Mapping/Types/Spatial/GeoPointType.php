<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Spatial;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;

/**
 * A geographic point. Used when sorting by distance (GeoDistanceSort), in decay functions
 * and in DistanceFeatureQuery.
 *
 * Elasticsearch accepts the value in several shapes - an object {"lat":…,"lon":…}, an array
 * [lon, lat], a string "lat,lon" or a geohash.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/geo-point.html
 */
#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
final class GeoPointType extends AbstractType
{
    /**
     * @param array<string, float>|string|null $null_value
     */
    public function __construct(
        private bool $ignore_malformed = false,
        private bool $ignore_z_value = true,
        private array|string|null $null_value = null,
        private bool $index = true,
        private bool $doc_values = true,
        ?string $name = null,
        ?string $context = null,
    ) {
        parent::__construct();

        $this->context = $context;
        $this->type = 'geo_point';
        if (null !== $name && $name !== '') {
            $this->setName($name);
        }
    }

    public function isIgnoreMalformed(): bool
    {
        return $this->ignore_malformed;
    }

    public function setIgnoreMalformed(bool $ignore_malformed): void
    {
        $this->ignore_malformed = $ignore_malformed;
    }

    /**
     * false means a point carrying a third dimension fails with an error instead of it being dropped.
     */
    public function isIgnoreZValue(): bool
    {
        return $this->ignore_z_value;
    }

    public function setIgnoreZValue(bool $ignore_z_value): void
    {
        $this->ignore_z_value = $ignore_z_value;
    }

    /**
     * @return array<string, float>|string|null
     */
    public function getNullValue(): array|string|null
    {
        return $this->null_value;
    }

    /**
     * @param array<string, float>|string|null $null_value
     */
    public function setNullValue(array|string|null $null_value): void
    {
        $this->null_value = $null_value;
    }

    public function isIndex(): bool
    {
        return $this->index;
    }

    public function setIndex(bool $index): void
    {
        $this->index = $index;
    }

    public function isDocValues(): bool
    {
        return $this->doc_values;
    }

    public function setDocValues(bool $doc_values): void
    {
        $this->doc_values = $doc_values;
    }

    public function getCollection(): ArrayCollection
    {
        $collection = parent::getCollection();

        if ($this->ignore_malformed) {
            $collection->set('ignore_malformed', true);
        }

        if (false === $this->ignore_z_value) {
            $collection->set('ignore_z_value', false);
        }

        if (null !== $this->null_value) {
            $collection->set('null_value', $this->null_value);
        }

        if (false === $this->index) {
            $collection->set('index', false);
        }

        if (false === $this->doc_values) {
            $collection->set('doc_values', false);
        }

        return $collection;
    }
}
