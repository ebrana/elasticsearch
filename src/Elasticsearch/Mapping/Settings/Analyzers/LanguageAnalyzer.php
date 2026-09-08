<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Analyzers;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractAnalyzer;
use Elasticsearch\Mapping\Settings\Analyzers\Concerns\WithStopwords;
use Elasticsearch\Mapping\Settings\Analyzers\Enums\AnalyzerLanguage;

/**
 * A built-in language analyzer (czech, english, ...) - it already contains a tokenizer, lowercase,
 * stopwords and a stemmer for the given language.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-lang-analyzer.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class LanguageAnalyzer extends AbstractAnalyzer
{
    use WithStopwords;

    /** @var string[]|null */
    private ?array $stem_exclusion;

    /**
     * @param string[]|string|null $stopwords
     * @param string[]|null $stem_exclusion
     */
    public function __construct(
        string $name,
        AnalyzerLanguage $language,
        array|string|null $stopwords = null,
        ?string $stopwords_path = null,
        ?array $stem_exclusion = null,
    ) {
        parent::__construct($name, $language->value);

        $this->setStopwords($stopwords);
        $this->setStopwordsPath($stopwords_path);
        $this->stem_exclusion = $stem_exclusion;
    }

    public function getLanguage(): AnalyzerLanguage
    {
        // the type of a built-in language analyzer is at the same time the language name
        return AnalyzerLanguage::from($this->getType());
    }

    /**
     * @return string[]|null
     */
    public function getStemExclusion(): ?array
    {
        return $this->stem_exclusion;
    }

    public function addStemExclusion(string $value): void
    {
        if (null === $this->stem_exclusion) {
            $this->stem_exclusion = [];
        }
        $this->stem_exclusion[] = $value;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        $this->provideStopwords($data);

        if ($this->stem_exclusion) {
            $data['stem_exclusion'] = $this->stem_exclusion;
        }

        return $data;
    }
}
