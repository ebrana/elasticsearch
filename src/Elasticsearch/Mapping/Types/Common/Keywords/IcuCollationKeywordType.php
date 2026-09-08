<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common\Keywords;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;

/**
 * A keyword sorted by the rules of a given language - in Czech "Č" then sorts right after "C"
 * and not after "Z". Typically used as a sub-field with index: false, for sorting only.
 *
 * BEWARE: requires the `analysis-icu` plugin. Without it Elasticsearch refuses to create the index
 * with "No mapper found for type [icu_collation_keyword]".
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/plugins/current/analysis-icu.html
 */
#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
final class IcuCollationKeywordType extends AbstractType
{
    /**
     * @param string|null $language ISO language code, e.g. "cs"
     * @param string|null $country ISO country code, e.g. "CZ"
     * @param string|null $strength primary, secondary, tertiary, quaternary or identical
     * @param string|null $decomposition no or canonical
     * @param string|null $alternate shifted or non-ignorable
     * @param string|null $case_first lower or upper
     */
    public function __construct(
        private ?string $language = null,
        private ?string $country = null,
        private ?string $variant = null,
        private ?string $strength = null,
        private ?string $decomposition = null,
        private ?string $alternate = null,
        private ?string $case_first = null,
        private ?bool $case_level = null,
        private ?bool $numeric = null,
        private bool $index = true,
        private bool $doc_values = true,
        private bool $store = false,
        private ?string $null_value = null,
        ?string $name = null,
        ?string $context = null,
    ) {
        parent::__construct();

        $this->context = $context;
        $this->type = 'icu_collation_keyword';
        if (null !== $name && $name !== '') {
            $this->setName($name);
        }
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): void
    {
        $this->language = $language;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getVariant(): ?string
    {
        return $this->variant;
    }

    public function setVariant(?string $variant): void
    {
        $this->variant = $variant;
    }

    public function getStrength(): ?string
    {
        return $this->strength;
    }

    public function setStrength(?string $strength): void
    {
        $this->strength = $strength;
    }

    public function getDecomposition(): ?string
    {
        return $this->decomposition;
    }

    public function setDecomposition(?string $decomposition): void
    {
        $this->decomposition = $decomposition;
    }

    public function getAlternate(): ?string
    {
        return $this->alternate;
    }

    public function setAlternate(?string $alternate): void
    {
        $this->alternate = $alternate;
    }

    public function getCaseFirst(): ?string
    {
        return $this->case_first;
    }

    public function setCaseFirst(?string $case_first): void
    {
        $this->case_first = $case_first;
    }

    public function getCaseLevel(): ?bool
    {
        return $this->case_level;
    }

    public function setCaseLevel(?bool $case_level): void
    {
        $this->case_level = $case_level;
    }

    /**
     * true sorts numbers by value, not character by character ("2" before "10").
     */
    public function getNumeric(): ?bool
    {
        return $this->numeric;
    }

    public function setNumeric(?bool $numeric): void
    {
        $this->numeric = $numeric;
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

    public function getNullValue(): ?string
    {
        return $this->null_value;
    }

    public function setNullValue(?string $null_value): void
    {
        $this->null_value = $null_value;
    }

    public function getCollection(): ArrayCollection
    {
        $collection = parent::getCollection();

        $options = [
            'language'      => $this->language,
            'country'       => $this->country,
            'variant'       => $this->variant,
            'strength'      => $this->strength,
            'decomposition' => $this->decomposition,
            'alternate'     => $this->alternate,
            'case_first'    => $this->case_first,
            'case_level'    => $this->case_level,
            'numeric'       => $this->numeric,
            'null_value'    => $this->null_value,
        ];

        foreach ($options as $key => $value) {
            if (null !== $value) {
                $collection->set($key, $value);
            }
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

        return $collection;
    }
}
