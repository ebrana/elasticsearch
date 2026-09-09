<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Analyzers;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractAnalyzer;
use Elasticsearch\Mapping\Settings\Analyzers\Concerns\WithStopwords;

/**
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-standard-analyzer.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class StandardAnalyzer extends AbstractAnalyzer
{
    use WithStopwords;

    public const int DEFAULT_MAX_TOKEN_LENGTH = 255;

    /**
     * @param string[]|string|null $stopwords
     */
    public function __construct(
        string $name,
        private int $max_token_length = self::DEFAULT_MAX_TOKEN_LENGTH,
        array|string|null $stopwords = null,
        ?string $stopwords_path = null,
    ) {
        parent::__construct($name, 'standard');

        $this->setStopwords($stopwords);
        $this->setStopwordsPath($stopwords_path);
    }

    public function getMaxTokenLength(): int
    {
        return $this->max_token_length;
    }

    public function setMaxTokenLength(int $max_token_length): void
    {
        $this->max_token_length = $max_token_length;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (self::DEFAULT_MAX_TOKEN_LENGTH !== $this->max_token_length) {
            $data['max_token_length'] = $this->max_token_length;
        }

        $this->provideStopwords($data);

        return $data;
    }
}
