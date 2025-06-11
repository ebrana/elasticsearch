<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractFilter;

/**
 * @deprecated Use StopFilter instead
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class StopAbstractFilter extends AbstractFilter
{
    /**
     * @param string[]|string $stopwords
     */
    public function __construct(
        string $name,
        private readonly array|string $stopwords,
        private readonly ?string $stopwords_path = null,
        private readonly bool $ignore_case = false,
        private readonly bool $remove_trailing = true
    ) {
        parent::__construct($name, 'stop');
    }

    /**
     * @return string[]|string
     */
    public function getStopwords(): array|string
    {
        return $this->stopwords;
    }

    public function getStopwordsPath(): ?string
    {
        return $this->stopwords_path;
    }

    public function isIgnoreCase(): bool
    {
        return $this->ignore_case;
    }

    public function isRemoveTrailing(): bool
    {
        return $this->remove_trailing;
    }

    /**
     * @return array<string, array<string>|bool|string>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (!empty($this->getStopwords())) {
            $data['stopwords'] = $this->getStopwords();
        }

        if ($this->getStopwordsPath()) {
            $data['stopwords_path'] = $this->getStopwordsPath();
        }

        if ($this->isIgnoreCase()) {
            $data['ignore_case'] = true;
        }

        if ($this->isRemoveTrailing() === false) {
            $data['remove_trailing'] = false;
        }

        return $data;
    }
}
