<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Resolvers\AnalysisResolver;

use Elasticsearch\Mapping\Drivers\Factories\Tokenizers\CharGroupTokenizerFactory;
use Elasticsearch\Mapping\Drivers\Factories\Tokenizers\ClassicTokenizerFactory;
use Elasticsearch\Mapping\Drivers\Factories\Tokenizers\EdgeNgramTokenizerFactory;
use Elasticsearch\Mapping\Drivers\Factories\Tokenizers\KeywordTokenizerFactory;
use Elasticsearch\Mapping\Drivers\Factories\Tokenizers\LetterTokenizerFactory;
use Elasticsearch\Mapping\Drivers\Factories\Tokenizers\LowercaseTokenizerFactory;
use Elasticsearch\Mapping\Drivers\Factories\Tokenizers\NgramTokenizerFactory;
use Elasticsearch\Mapping\Drivers\Factories\Tokenizers\PathHierarchyTokenizerFactory;
use Elasticsearch\Mapping\Drivers\Factories\Tokenizers\PatternTokenizerFactory;
use Elasticsearch\Mapping\Drivers\Factories\Tokenizers\StandardTokenizerFactory;
use Elasticsearch\Mapping\Drivers\Factories\Tokenizers\UaxUrlEmailTokenizerFactory;
use Elasticsearch\Mapping\Drivers\Factories\Tokenizers\WhitespaceTokenizerFactory;
use Elasticsearch\Mapping\Settings\Analysis;
use stdClass;

final class TokenizerResolver
{
    /** @var string[] */
    private array $tokenizerFactories = [
        'edge_ngram'     => EdgeNgramTokenizerFactory::class,
        'ngram'          => NgramTokenizerFactory::class,
        'pattern'        => PatternTokenizerFactory::class,
        'standard'       => StandardTokenizerFactory::class,
        'keyword'        => KeywordTokenizerFactory::class,
        'whitespace'     => WhitespaceTokenizerFactory::class,
        'letter'         => LetterTokenizerFactory::class,
        'lowercase'      => LowercaseTokenizerFactory::class,
        'uax_url_email'  => UaxUrlEmailTokenizerFactory::class,
        'char_group'     => CharGroupTokenizerFactory::class,
        'path_hierarchy' => PathHierarchyTokenizerFactory::class,
        'classic'        => ClassicTokenizerFactory::class,
    ];

    /**
     * @deprecated Misspelled name, use resolveTokenizer().
     */
    public function resolvetTokenizer(stdClass $tokenizers, Analysis $analysis): void
    {
        $this->resolveTokenizer($tokenizers, $analysis);
    }

    public function resolveTokenizer(stdClass $tokenizers, Analysis $analysis): void
    {
        foreach ((array)$tokenizers as $name => $tokenizer) {
            if (isset($this->tokenizerFactories[$tokenizer->type])) {
                $factory = $this->tokenizerFactories[$tokenizer->type];
                $analysis->addTokenizer($factory::create($name, $tokenizer));
            }
        }
    }
}
