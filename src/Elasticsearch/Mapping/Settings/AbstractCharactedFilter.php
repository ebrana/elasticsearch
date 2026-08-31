<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings;

/**
 * @deprecated Preklep v nazvu, pouzij AbstractCharacterFilter. Zustava kvuli zpetne
 *             kompatibilite - vlastni char filtry, ktere ji dedi, fungujou dal.
 */
abstract class AbstractCharactedFilter extends AbstractCharacterFilter
{
}
