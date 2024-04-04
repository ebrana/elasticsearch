<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Tokenizers;

use Elasticsearch\Mapping\Settings\AbstractTokenizer;
use Elasticsearch\Mapping\Settings\Tokenizers\Enums\TokenChars;
use RuntimeException;

trait GramTrait
{
    private int $min_gram;
    private int $max_gram;

    /** @var TokenChars[] */
    private array $token_chars;

    /** @var string[]|null */
    private ?array $custom_token_chars;

    public function getMinGram(): int
    {
        return $this->min_gram;
    }

    public function setMinGram(int $min_gram): void
    {
        $this->min_gram = $min_gram;
    }

    public function getMaxGram(): int
    {
        return $this->max_gram;
    }

    public function setMaxGram(int $max_gram): void
    {
        $this->max_gram = $max_gram;
    }

    /**
     * @return TokenChars[]
     */
    public function getTokenChars(): array
    {
        return $this->token_chars;
    }

    /**
     * @param TokenChars[] $token_chars
     * @return void
     */
    public function setTokenChars(array $token_chars): void
    {
        $this->token_chars = $token_chars;
        $this->validate();
    }

    /**
     * @return string[]|null
     */
    public function getCustomTokenChars(): ?array
    {
        return $this->custom_token_chars;
    }

    /**
     * @param string[]|null $custom_token_chars
     * @return void
     */
    public function setCustomTokenChars(?array $custom_token_chars): void
    {
        $this->custom_token_chars = $custom_token_chars;
    }

    /**
     * @return array<string, array<string>|int|string>
     */
    public function toArray(): array
    {
        $data = AbstractTokenizer::toArray();

        if ($this->getMinGram() !== 1) {
            $data['min_gram'] = $this->getMinGram();
        }

        if ($this->getMaxGram() !== 2) {
            $data['max_gram'] = $this->getMaxGram();
        }

        if (!empty($this->getTokenChars())) {
            $data['token_chars'] = array_column($this->getTokenChars(), 'value');
        }

        if ($this->getCustomTokenChars() !== null) {
            $data['custom_token_chars'] = $this->getCustomTokenChars();
        }

        return $data;
    }

    private function validate(): void
    {
        $validTokenChars = array_column(TokenChars::cases(), 'value');
        foreach ($this->getTokenChars() as $tokenChar) {
            if (! $tokenChar instanceof TokenChars) {
                throw new RuntimeException('Invalid type. Expect TokenChars enum. Given "' . gettype($tokenChar) . '".');
            }
            if (!in_array($tokenChar->value, $validTokenChars, true)) {
                throw new RuntimeException(
                    sprintf('Value "%s" in token_chars is invalid. Valid values is "%s"',
                        $tokenChar->value,
                        implode(', ', $validTokenChars)
                    )
                );
            }
            if (TokenChars::CUSTOM === $tokenChar && null === $this->getCustomTokenChars()) {
                throw new RuntimeException('You set "custom" value in token_chars but custom_token_chars is empty.');
            }
        }
    }
}
