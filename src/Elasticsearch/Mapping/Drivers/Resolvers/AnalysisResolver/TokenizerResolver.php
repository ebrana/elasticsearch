<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Resolvers\AnalysisResolver;

use Elasticsearch\Mapping\Drivers\Factories\Tokenizers\EdgeNgramTokenizerFactory;
use Elasticsearch\Mapping\Drivers\Factories\Tokenizers\NgramTokenizerFactory;
use Elasticsearch\Mapping\Drivers\Factories\Tokenizers\PatternTokenizerFactory;
use Elasticsearch\Mapping\Drivers\Factories\Tokenizers\StandardTokenizerFactory;
use Elasticsearch\Mapping\Settings\Analysis;
use stdClass;

final class TokenizerResolver
{
    /** @var string[] */
    private array $tokenizerFactories = [
        'edge_ngram' => EdgeNgramTokenizerFactory::class,
        'ngram'      => NgramTokenizerFactory::class,
        'pattern'    => PatternTokenizerFactory::class,
        'standard'   => StandardTokenizerFactory::class,
    ];

    public function resolvetTokenizer(stdClass $tokenizers, Analysis $analysis): void
    {
        foreach ((array)$tokenizers as $name => $tokenizer) {
            if (isset($this->tokenizerFactories[$tokenizer->type])) {
                $factory = $this->tokenizerFactories[$tokenizer->type];
                $analysis->addTokenizer($factory::create($name, $tokenizer));
            }
        }
    }
}
