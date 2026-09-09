<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Elasticsearch\Mapping\Settings\AbstractFilter;
use Elasticsearch\Mapping\Settings\Filters\Enums\SynonymFormat;

/**
 * Spolecny zaklad pro synonym a synonym_graph. Synonyma se zadavaji bud vyctem,
 * cestou k souboru (synonyms_path), nebo jmenem synonym setu (synonyms_set).
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-synonym-tokenfilter.html
 */
abstract class AbstractSynonymFilter extends AbstractFilter
{
    /**
     * @param string[]|null $synonyms
     */
    public function __construct(
        string $name,
        string $type,
        private ?array $synonyms = null,
        private ?string $synonyms_path = null,
        private ?string $synonyms_set = null,
        private bool $expand = true,
        private bool $lenient = false,
        private ?SynonymFormat $format = null,
        private bool $updateable = false,
    ) {
        parent::__construct($name, $type);
    }

    /**
     * @return string[]|null
     */
    public function getSynonyms(): ?array
    {
        return $this->synonyms;
    }

    public function addSynonym(string $synonym): void
    {
        if (null === $this->synonyms) {
            $this->synonyms = [];
        }
        $this->synonyms[] = $synonym;
    }

    public function getSynonymsPath(): ?string
    {
        return $this->synonyms_path;
    }

    public function setSynonymsPath(?string $synonyms_path): void
    {
        $this->synonyms_path = $synonyms_path;
    }

    public function getSynonymsSet(): ?string
    {
        return $this->synonyms_set;
    }

    public function setSynonymsSet(?string $synonyms_set): void
    {
        $this->synonyms_set = $synonyms_set;
    }

    public function isExpand(): bool
    {
        return $this->expand;
    }

    public function setExpand(bool $expand): void
    {
        $this->expand = $expand;
    }

    public function isLenient(): bool
    {
        return $this->lenient;
    }

    public function setLenient(bool $lenient): void
    {
        $this->lenient = $lenient;
    }

    public function getFormat(): ?SynonymFormat
    {
        return $this->format;
    }

    public function setFormat(?SynonymFormat $format): void
    {
        $this->format = $format;
    }

    public function isUpdateable(): bool
    {
        return $this->updateable;
    }

    public function setUpdateable(bool $updateable): void
    {
        $this->updateable = $updateable;
    }

    /**
     * @return array<string, array<string>|bool|string>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if ($this->synonyms) {
            $data['synonyms'] = $this->synonyms;
        }

        if ($this->synonyms_path) {
            $data['synonyms_path'] = $this->synonyms_path;
        }

        if ($this->synonyms_set) {
            $data['synonyms_set'] = $this->synonyms_set;
        }

        if (false === $this->expand) {
            $data['expand'] = false;
        }

        if ($this->lenient) {
            $data['lenient'] = true;
        }

        if ($this->format) {
            $data['format'] = $this->format->value;
        }

        if ($this->updateable) {
            $data['updateable'] = true;
        }

        return $data;
    }
}
