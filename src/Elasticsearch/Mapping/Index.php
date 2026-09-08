<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Exceptions\DuplicityPropertyException;
use Elasticsearch\Mapping\Exceptions\EmptyIndexNameException;
use Elasticsearch\Mapping\Settings\Analysis;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\ValidatorInterface;
use RuntimeException;

#[Attribute(Attribute::TARGET_CLASS)]
final class Index
{
    /**
     *
        Lowercase only
        Cannot include \, /, *, ?, ", <, >, |, space (the character, not the word), ,, #
        Indices prior to 7.0 could contain a colon (:), but that's been deprecated and won't be supported in 7.0+
        Cannot start with -, _, +
        Cannot be . or ..
        Cannot be longer than 255 characters
     */
    private ?string $name;
    private ArrayCollection $properties;
    private ?Analysis $analysis = null;
    private string $entityClass;

    public function __construct(
        ?string $name = null,
        private int $max_result_window = 10000,
        /** @var class-string|null */
        private readonly ?string $postEventClass = null,
        private ?int $number_of_shards = null,
        private ?int $number_of_replicas = null,
        private ?string $refresh_interval = null,
        private ?int $max_ngram_diff = null,
        private ?int $max_shingle_diff = null,
    ) {
        $this->properties = new ArrayCollection();
        $this->name = $name ? strtolower($name) : $name;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function setEntityClass(string $entityClass): void
    {
        $this->entityClass = $entityClass;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name ? strtolower($name) : $name;
    }

    /**
     * @return ArrayCollection<AbstractType> $properties
     */
    public function getProperties(): ArrayCollection
    {
        return $this->properties;
    }

    public function getAnalysis(): ?Analysis
    {
        return $this->analysis;
    }

    public function setAnalysis(?Analysis $analysis): void
    {
        $this->analysis = $analysis;
    }

    public function getMaxResultWindow(): int
    {
        return $this->max_result_window;
    }

    public function setMaxResultWindow(int $max_result_window): void
    {
        $this->max_result_window = $max_result_window;
    }

    public function getNumberOfShards(): ?int
    {
        return $this->number_of_shards;
    }

    public function setNumberOfShards(?int $number_of_shards): void
    {
        $this->number_of_shards = $number_of_shards;
    }

    public function getNumberOfReplicas(): ?int
    {
        return $this->number_of_replicas;
    }

    public function setNumberOfReplicas(?int $number_of_replicas): void
    {
        $this->number_of_replicas = $number_of_replicas;
    }

    /**
     * E.g. "1s" or "-1" to turn it off; during a bulk reindex it pays off to turn it off
     * and restore it once finished.
     */
    public function getRefreshInterval(): ?string
    {
        return $this->refresh_interval;
    }

    public function setRefreshInterval(?string $refresh_interval): void
    {
        $this->refresh_interval = $refresh_interval;
    }

    /**
     * Maximalni rozdil mezi min_gram a max_gram u ngram tokenizeru (ES default 1).
     */
    public function getMaxNgramDiff(): ?int
    {
        return $this->max_ngram_diff;
    }

    public function setMaxNgramDiff(?int $max_ngram_diff): void
    {
        $this->max_ngram_diff = $max_ngram_diff;
    }

    /**
     * Maximalni rozdil mezi min_shingle_size a max_shingle_size (ES default 3).
     */
    public function getMaxShingleDiff(): ?int
    {
        return $this->max_shingle_diff;
    }

    public function setMaxShingleDiff(?int $max_shingle_diff): void
    {
        $this->max_shingle_diff = $max_shingle_diff;
    }

    /**
     * @throws DuplicityPropertyException
     * @throws RuntimeException
     */
    public function addProperty(AbstractType $type): void
    {
        if ($type instanceof ValidatorInterface) {
            $type->validate();
        }
        $name = $type->getName();
        if ($this->properties->containsKey($name)) {
            throw new DuplicityPropertyException($type, $name);
        }

        $this->properties->set($name, $type);
    }

    /**
     * @throws \Elasticsearch\Mapping\Exceptions\EmptyIndexNameException
     */
    public function getNameWithPrefix(?string $prefix = null): string
    {
        $name = $this->getName();
        if (null === $name || '' === $name) {
            throw new EmptyIndexNameException();
        }

        return $prefix . $name;
    }

    /**
     * @return class-string|null
     */
    public function getPostEventClass(): ?string
    {
        return $this->postEventClass;
    }
}
