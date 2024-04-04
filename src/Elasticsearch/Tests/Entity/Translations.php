<?php declare(strict_types=1);

namespace Elasticsearch\Tests\Entity;

class Translations
{
    /** @var string */
    private $lang;

    /** @var float */
    private $value;

    /** @var string */
    private $description;

    /** @var string|null */
    private $test1;

    /** @var string|null */
    private $test2;

    /** @var string|null */
    private $test3;

    /** @var string|null */
    private $sellingPrice;

    /** @var string|null */
    private $sellingPriceWithVat;

    public function __construct(
        string $lang,
        float $value,
        string $description,
        string $test1,
        string $test2
    ) {
        $this->lang = $lang;
        $this->value = $value;
        $this->description = $description;
        $this->test1 = $test1;
        $this->test2 = $test2;
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTest1(): ?string
    {
        return $this->test1;
    }

    public function setTest1(?string $test1): void
    {
        $this->test1 = $test1;
    }

    public function getTest2(): ?string
    {
        return $this->test2;
    }

    public function setTest2(?string $test2): void
    {
        $this->test2 = $test2;
    }

    public function getTest3(): ?string
    {
        return $this->test3;
    }

    public function setTest3(?string $test3): void
    {
        $this->test3 = $test3;
    }

    public function getSellingPrice(): ?string
    {
        return $this->sellingPrice;
    }

    public function setSellingPrice(?string $sellingPrice): void
    {
        $this->sellingPrice = $sellingPrice;
    }

    public function getSellingPriceWithVat(): ?string
    {
        return $this->sellingPriceWithVat;
    }

    public function setSellingPriceWithVat(?string $sellingPriceWithVat): void
    {
        $this->sellingPriceWithVat = $sellingPriceWithVat;
    }

    public function getsellingPriceWithVatKeyword(): ?string
    {
        return $this->getSellingPriceWithVat();
    }

    public function getTest4(): string
    {
        return '';
    }
}
