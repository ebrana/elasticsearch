<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Tokenizers;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractTokenizer;

#[Attribute(Attribute::TARGET_CLASS)]
class StandardTokenizer extends AbstractTokenizer
{
    public function __construct(
        string $name,
        private int $max_token_length = 255
    ) {
        parent::__construct($name, 'standard');
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
     * @return array<string, array<string>|int|string>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if ($this->getMaxTokenLength() !== 255) {
            $data['max_token_length'] = $this->getMaxTokenLength();
        }

        return $data;
    }
}
