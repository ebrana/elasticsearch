<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Analyzers;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractAnalyzer;
use Elasticsearch\Mapping\Settings\Analyzers\Concerns\WithStopwords;

/**
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-fingerprint-analyzer.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class FingerprintAnalyzer extends AbstractAnalyzer
{
    use WithStopwords;

    public const string DEFAULT_SEPARATOR = ' ';
    public const int DEFAULT_MAX_OUTPUT_SIZE = 255;

    /**
     * @param string[]|string|null $stopwords
     */
    public function __construct(
        string $name,
        private string $separator = self::DEFAULT_SEPARATOR,
        private int $max_output_size = self::DEFAULT_MAX_OUTPUT_SIZE,
        array|string|null $stopwords = null,
        ?string $stopwords_path = null,
    ) {
        parent::__construct($name, 'fingerprint');

        $this->setStopwords($stopwords);
        $this->setStopwordsPath($stopwords_path);
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function setSeparator(string $separator): void
    {
        $this->separator = $separator;
    }

    public function getMaxOutputSize(): int
    {
        return $this->max_output_size;
    }

    public function setMaxOutputSize(int $max_output_size): void
    {
        $this->max_output_size = $max_output_size;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (self::DEFAULT_SEPARATOR !== $this->separator) {
            $data['separator'] = $this->separator;
        }

        if (self::DEFAULT_MAX_OUTPUT_SIZE !== $this->max_output_size) {
            $data['max_output_size'] = $this->max_output_size;
        }

        $this->provideStopwords($data);

        return $data;
    }
}
