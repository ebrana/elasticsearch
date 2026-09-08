<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @deprecated Misspelled name, use MappingMetadata. Kept for backward compatibility.
 */
readonly class MappingMetada extends MappingMetadata
{
    /**
     * @param ArrayCollection<string, Index> $metadata
     */
    public function __construct(ArrayCollection $metadata)
    {
        trigger_error(
            sprintf('Class "%s" is deprecated, use "%s" instead.', self::class, MappingMetadata::class),
            E_USER_DEPRECATED
        );

        parent::__construct($metadata);
    }
}
