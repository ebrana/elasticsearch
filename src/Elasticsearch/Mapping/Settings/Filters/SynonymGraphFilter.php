<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\Filters\Enums\SynonymFormat;

/**
 * Zvlada i viceslovna synonyma, ale lze ho pouzit jen jako search analyzer.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-synonym-graph-tokenfilter.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class SynonymGraphFilter extends AbstractSynonymFilter
{
    /**
     * @param string[]|null $synonyms
     */
    public function __construct(
        string $name,
        ?array $synonyms = null,
        ?string $synonyms_path = null,
        ?string $synonyms_set = null,
        bool $expand = true,
        bool $lenient = false,
        ?SynonymFormat $format = null,
        bool $updateable = false,
    ) {
        parent::__construct(
            $name,
            'synonym_graph',
            $synonyms,
            $synonyms_path,
            $synonyms_set,
            $expand,
            $lenient,
            $format,
            $updateable
        );
    }
}
