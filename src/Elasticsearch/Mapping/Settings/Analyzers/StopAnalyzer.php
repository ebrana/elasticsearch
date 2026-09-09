<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Analyzers;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractAnalyzer;
use Elasticsearch\Mapping\Settings\Analyzers\Concerns\WithStopwords;

/**
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-stop-analyzer.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class StopAnalyzer extends AbstractAnalyzer
{
    use WithStopwords;

    /**
     * @param string[]|string|null $stopwords
     */
    public function __construct(
        string $name,
        array|string|null $stopwords = null,
        ?string $stopwords_path = null,
    ) {
        parent::__construct($name, 'stop');

        $this->setStopwords($stopwords);
        $this->setStopwordsPath($stopwords_path);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        $this->provideStopwords($data);

        return $data;
    }
}
