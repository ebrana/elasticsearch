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
        private readonly int $max_result_window = 10000,
        /** @var class-string|null */
        private readonly ?string $postEventClass = null,
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
