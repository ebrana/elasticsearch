<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Specialized;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;

/**
 * Cislo urcene k ovlivneni skore pres RankFeatureQuery - napr. popularita nebo pocet prodeju.
 * Hodnota musi byt kladna.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/rank-feature.html
 */
#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
final class RankFeatureType extends AbstractType
{
    public function __construct(
        private bool $positive_score_impact = true,
        ?string $name = null,
        ?string $context = null,
    ) {
        parent::__construct();

        $this->context = $context;
        $this->type = 'rank_feature';
        if (null !== $name && $name !== '') {
            $this->setName($name);
        }
    }

    /**
     * false znamena, ze vyssi hodnota skore snizuje (napr. doba doruceni).
     */
    public function isPositiveScoreImpact(): bool
    {
        return $this->positive_score_impact;
    }

    public function setPositiveScoreImpact(bool $positive_score_impact): void
    {
        $this->positive_score_impact = $positive_score_impact;
    }

    public function getCollection(): ArrayCollection
    {
        $collection = parent::getCollection();

        if (false === $this->positive_score_impact) {
            $collection->set('positive_score_impact', false);
        }

        return $collection;
    }
}
