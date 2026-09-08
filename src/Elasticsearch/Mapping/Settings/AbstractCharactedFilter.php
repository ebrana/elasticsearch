<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings;

/**
 * @deprecated Misspelled name, use AbstractCharacterFilter. Kept for backward compatibility -
 *             custom char filters extending it keep working.
 */
abstract class AbstractCharactedFilter extends AbstractCharacterFilter
{
    public function __construct(string $name, string $type)
    {
        trigger_error(
            sprintf('Class "%s" is deprecated, extend "%s" instead.', self::class, AbstractCharacterFilter::class),
            E_USER_DEPRECATED
        );

        parent::__construct($name, $type);
    }
}
