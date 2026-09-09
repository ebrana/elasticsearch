<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractFilter;

/**
 * Odstranuje elize ("l'avion" -> "avion").
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-elision-tokenfilter.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class ElisionFilter extends AbstractFilter
{
    /**
     * @param string[]|null $articles
     */
    public function __construct(
        string $name,
        private ?array $articles = null,
        private ?string $articles_path = null,
        private bool $articles_case = false,
    ) {
        parent::__construct($name, 'elision');
    }

    /**
     * @return string[]|null
     */
    public function getArticles(): ?array
    {
        return $this->articles;
    }

    public function addArticle(string $article): void
    {
        if (null === $this->articles) {
            $this->articles = [];
        }
        $this->articles[] = $article;
    }

    public function getArticlesPath(): ?string
    {
        return $this->articles_path;
    }

    public function setArticlesPath(?string $articles_path): void
    {
        $this->articles_path = $articles_path;
    }

    public function isArticlesCase(): bool
    {
        return $this->articles_case;
    }

    public function setArticlesCase(bool $articles_case): void
    {
        $this->articles_case = $articles_case;
    }

    /**
     * @return array<string, array<string>|bool|string>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if ($this->articles) {
            $data['articles'] = $this->articles;
        }

        if ($this->articles_path) {
            $data['articles_path'] = $this->articles_path;
        }

        if ($this->articles_case) {
            $data['articles_case'] = true;
        }

        return $data;
    }
}
