<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Tokenizers;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractTokenizer;
use Elasticsearch\Mapping\Settings\Tokenizers\Enums\TokenChars;

#[Attribute(Attribute::TARGET_CLASS)]
class NgramTokenizer extends AbstractTokenizer implements GramInterface
{
    use GramTrait;

    /**
     * @param TokenChars[]  $token_chars
     * @param string[]|null $custom_token_chars
     */
    public function __construct(
        string $name,
        int $min_gram = 1,
        int $max_gram = 2,
        array $token_chars = [],
        ?array $custom_token_chars = null
    ) {
        parent::__construct($name, 'ngram');

        $this->min_gram = $min_gram;
        $this->max_gram = $max_gram;
        $this->token_chars = $token_chars;
        $this->custom_token_chars = $custom_token_chars;
        $this->validate();
    }
}
