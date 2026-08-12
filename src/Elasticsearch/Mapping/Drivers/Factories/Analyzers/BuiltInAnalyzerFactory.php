<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Analyzers;

use Elasticsearch\Mapping\Settings\AbstractAnalyzer;
use Elasticsearch\Mapping\Settings\Analyzers\Enums\AnalyzerLanguage;
use Elasticsearch\Mapping\Settings\Analyzers\FingerprintAnalyzer;
use Elasticsearch\Mapping\Settings\Analyzers\KeywordAnalyzer;
use Elasticsearch\Mapping\Settings\Analyzers\LanguageAnalyzer;
use Elasticsearch\Mapping\Settings\Analyzers\PatternAnalyzer;
use Elasticsearch\Mapping\Settings\Analyzers\SimpleAnalyzer;
use Elasticsearch\Mapping\Settings\Analyzers\StandardAnalyzer;
use Elasticsearch\Mapping\Settings\Analyzers\StopAnalyzer;
use Elasticsearch\Mapping\Settings\Analyzers\WhitespaceAnalyzer;
use stdClass;

/**
 * Vytvari vestavene analyzery ze JSON definice. Jazykove analyzery se poznaji podle toho,
 * ze je jejich `type` jmenem jazyka (czech, english, ...).
 */
final class BuiltInAnalyzerFactory
{
    public static function supports(string $type): bool
    {
        return in_array($type, ['standard', 'simple', 'whitespace', 'keyword', 'stop', 'pattern', 'fingerprint'], true)
            || null !== AnalyzerLanguage::tryFrom($type);
    }

    /**
     * Podle typu se cte: max_token_length (standard), buffer_size (keyword),
     * pattern/flags/lowercase (pattern), separator/max_output_size (fingerprint),
     * stem_exclusion (jazykove analyzery) a stopwords/stopwords_path.
     */
    public static function create(string $name, string $type, stdClass $configuration): AbstractAnalyzer
    {
        $language = AnalyzerLanguage::tryFrom($type);
        if (null !== $language) {
            return new LanguageAnalyzer(
                $name,
                $language,
                $configuration->stopwords ?? null,
                $configuration->stopwords_path ?? null,
                $configuration->stem_exclusion ?? null
            );
        }

        return match ($type) {
            'standard' => new StandardAnalyzer(
                $name,
                $configuration->max_token_length ?? StandardAnalyzer::DEFAULT_MAX_TOKEN_LENGTH,
                $configuration->stopwords ?? null,
                $configuration->stopwords_path ?? null
            ),
            'stop' => new StopAnalyzer(
                $name,
                $configuration->stopwords ?? null,
                $configuration->stopwords_path ?? null
            ),
            'pattern' => new PatternAnalyzer(
                $name,
                $configuration->pattern ?? PatternAnalyzer::DEFAULT_PATTERN,
                $configuration->flags ?? null,
                (bool)($configuration->lowercase ?? true),
                $configuration->stopwords ?? null,
                $configuration->stopwords_path ?? null
            ),
            'fingerprint' => new FingerprintAnalyzer(
                $name,
                $configuration->separator ?? FingerprintAnalyzer::DEFAULT_SEPARATOR,
                $configuration->max_output_size ?? FingerprintAnalyzer::DEFAULT_MAX_OUTPUT_SIZE,
                $configuration->stopwords ?? null,
                $configuration->stopwords_path ?? null
            ),
            'keyword' => new KeywordAnalyzer($name, $configuration->buffer_size ?? KeywordAnalyzer::DEFAULT_BUFFER_SIZE),
            'whitespace' => new WhitespaceAnalyzer($name),
            default => new SimpleAnalyzer($name),
        };
    }
}
