<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use LogicException;

/**
 * A property that is written into the document (and therefore into `_source`) but is never part
 * of the mapping, so it is neither indexed nor counted against `index.mapping.total_fields.limit`.
 *
 * The enclosing level (the index or the parent ObjectType) must have `dynamic: false` - with the
 * ES default `true` Elasticsearch would map the field dynamically on the first document, with
 * `strict` it would reject the document. MetadataRequestFactory enforces that.
 *
 * The value may be null, a scalar or an array (of scalars / nested arrays), the default document
 * builder and the resolver traits put it into the document as it is.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class SourceOnly extends AbstractType
{
    public function __construct(?string $name = null, ?string $context = null)
    {
        parent::__construct();

        $this->type = 'source_only';
        $this->context = $context;
        if (null !== $name && '' !== $name) {
            $this->setName($name);
        }
    }

    /**
     * @throws LogicException always - a SourceOnly field has no mapping definition
     */
    public function getCollection(): ArrayCollection
    {
        throw new LogicException(sprintf(
            'SourceOnly field "%s" is never part of the mapping.',
            $this->getFieldName() ?? $this->getName()
        ));
    }
}
