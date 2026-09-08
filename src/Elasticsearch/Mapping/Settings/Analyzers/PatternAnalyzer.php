<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Analyzers;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractAnalyzer;
use Elasticsearch\Mapping\Settings\Analyzers\Concerns\WithStopwords;

/**
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-pattern-analyzer.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class PatternAnalyzer extends AbstractAnalyzer
{
    use WithStopwords;

    public const string DEFAULT_PATTERN = '\W+';

    /**
     * @param string[]|string|null $stopwords
     */
    public function __construct(
        string $name,
        private string $pattern = self::DEFAULT_PATTERN,
        private ?string $flags = null,
        private bool $lowercase = true,
        array|string|null $stopwords = null,
        ?string $stopwords_path = null,
    ) {
        parent::__construct($name, 'pattern');

        $this->setStopwords($stopwords);
        $this->setStopwordsPath($stopwords_path);
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    public function getFlags(): ?string
    {
        return $this->flags;
    }

    public function setFlags(?string $flags): void
    {
        $this->flags = $flags;
    }

    public function isLowercase(): bool
    {
        return $this->lowercase;
    }

    public function setLowercase(bool $lowercase): void
    {
        $this->lowercase = $lowercase;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['pattern'] = $this->pattern;

        if ($this->flags) {
            $data['flags'] = $this->flags;
        }

        if (false === $this->lowercase) {
            $data['lowercase'] = false;
        }

        $this->provideStopwords($data);

        return $data;
    }
}
