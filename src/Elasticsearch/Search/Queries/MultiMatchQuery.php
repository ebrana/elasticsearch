<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Elasticsearch\Search\Queries\Enums\MultiMatchType;
use Generator;

readonly class MultiMatchQuery implements Query
{
    /**
     * @param string[]    $fields
     */
    public function __construct(
        private string $query,
        private array $fields,
        private ?string $fuzziness = null,
        private ?MultiMatchType $type = null
    ) {
    }

    public function toArray(): Generator
    {
        $multiMatch = [
            'query'  => $this->query,
            'fields' => $this->fields,
        ];

        if ($this->fuzziness) {
            $multiMatch['fuzziness'] = $this->fuzziness;
        }

        if ($this->type) {
            $multiMatch['type'] = $this->type->value;
        }

        yield 'multi_match' => $multiMatch;
    }
}
