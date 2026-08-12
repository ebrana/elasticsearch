<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractFilter;

/**
 * Oznaci tokeny jako klicova slova, takze je nasledujici stemmer nechá být.
 * Musi byt v poradi filtru pred stemmerem.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-keyword-marker-tokenfilter.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class KeywordMarkerFilter extends AbstractFilter
{
    /**
     * @param string[]|null $keywords
     */
    public function __construct(
        string $name,
        private ?array $keywords = null,
        private ?string $keywords_path = null,
        private ?string $keywords_pattern = null,
        private bool $ignore_case = false,
    ) {
        parent::__construct($name, 'keyword_marker');
    }

    /**
     * @return string[]|null
     */
    public function getKeywords(): ?array
    {
        return $this->keywords;
    }

    public function addKeyword(string $keyword): void
    {
        if (null === $this->keywords) {
            $this->keywords = [];
        }
        $this->keywords[] = $keyword;
    }

    public function getKeywordsPath(): ?string
    {
        return $this->keywords_path;
    }

    public function setKeywordsPath(?string $keywords_path): void
    {
        $this->keywords_path = $keywords_path;
    }

    public function getKeywordsPattern(): ?string
    {
        return $this->keywords_pattern;
    }

    public function setKeywordsPattern(?string $keywords_pattern): void
    {
        $this->keywords_pattern = $keywords_pattern;
    }

    public function isIgnoreCase(): bool
    {
        return $this->ignore_case;
    }

    public function setIgnoreCase(bool $ignore_case): void
    {
        $this->ignore_case = $ignore_case;
    }

    /**
     * @return array<string, array<string>|bool|string>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if ($this->keywords) {
            $data['keywords'] = $this->keywords;
        }

        if ($this->keywords_path) {
            $data['keywords_path'] = $this->keywords_path;
        }

        if ($this->keywords_pattern) {
            $data['keywords_pattern'] = $this->keywords_pattern;
        }

        if ($this->ignore_case) {
            $data['ignore_case'] = true;
        }

        return $data;
    }
}
