<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Analyzers\Concerns;

trait WithStopwords
{
    /** @var string[]|string|null */
    private array|string|null $stopwords = null;
    private ?string $stopwords_path = null;

    /**
     * Either a custom word list, or a predefined Elasticsearch set (e.g. "_czech_", "_none_").
     *
     * @param string[]|string|null $stopwords
     */
    public function setStopwords(array|string|null $stopwords): void
    {
        $this->stopwords = $stopwords;
    }

    /**
     * @return string[]|string|null
     */
    public function getStopwords(): array|string|null
    {
        return $this->stopwords;
    }

    public function setStopwordsPath(?string $stopwords_path): void
    {
        $this->stopwords_path = $stopwords_path;
    }

    public function getStopwordsPath(): ?string
    {
        return $this->stopwords_path;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function provideStopwords(array &$data): void
    {
        if (null !== $this->stopwords && [] !== $this->stopwords && '' !== $this->stopwords) {
            $data['stopwords'] = $this->stopwords;
        }

        if ($this->stopwords_path) {
            $data['stopwords_path'] = $this->stopwords_path;
        }
    }
}
