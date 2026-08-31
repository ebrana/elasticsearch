<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Helpers;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Parametry, ktere maji spolecne date a date_nanos.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/date.html
 */
trait DateOptionsTrait
{
    private ?string $format = null;
    private ?string $locale = null;
    private ?string $null_value = null;
    private bool $index = true;
    private bool $doc_values = true;
    private bool $store = false;
    private bool $ignore_malformed = false;

    /**
     * Napr. "yyyy-MM-dd" nebo "strict_date_optional_time||epoch_millis".
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): void
    {
        $this->format = $format;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function getNullValue(): ?string
    {
        return $this->null_value;
    }

    public function setNullValue(?string $null_value): void
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

    public function isStore(): bool
    {
        return $this->store;
    }

    public function setStore(bool $store): void
    {
        $this->store = $store;
    }

    /**
     * Neplatne datum se pri indexaci preskoci misto vyhozeni chyby.
     */
    public function isIgnoreMalformed(): bool
    {
        return $this->ignore_malformed;
    }

    public function setIgnoreMalformed(bool $ignore_malformed): void
    {
        $this->ignore_malformed = $ignore_malformed;
    }

    /**
     * @param ArrayCollection<string, mixed> $collection
     */
    private function provideDateOptions(ArrayCollection $collection): void
    {
        if (null !== $this->format) {
            $collection->set('format', $this->format);
        }

        if (null !== $this->locale) {
            $collection->set('locale', $this->locale);
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

        if ($this->store) {
            $collection->set('store', true);
        }

        if ($this->ignore_malformed) {
            $collection->set('ignore_malformed', true);
        }
    }
}
