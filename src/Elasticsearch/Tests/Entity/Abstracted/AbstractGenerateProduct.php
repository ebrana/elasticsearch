<?php

declare(strict_types=1);

namespace Elasticsearch\Tests\Entity\Abstracted;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Settings\Analyzer;
use Elasticsearch\Mapping\Settings\Filters\NgramAbstractFilter;
use Elasticsearch\Mapping\Settings\Tokenizers\Enums\TokenChars;
use Elasticsearch\Mapping\Settings\Tokenizers\NgramTokenizer;
use Elasticsearch\Mapping\Types\Common\Keywords\KeywordType;
use Elasticsearch\Mapping\Types\Common\Numeric\FloatType;
use Elasticsearch\Mapping\Types\Common\Numeric\IntegerType;
use Elasticsearch\Mapping\Types\Helpers\Metadata;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\NestedType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;
use Elasticsearch\Mapping\Types\Text\MatchOnlyTextType;
use Elasticsearch\Mapping\Types\Text\TextType;
use Elasticsearch\Tests\Entity\Translations;

#[Index(name: "AmproductsModule")]
#[Analyzer(name: "autocomplete_analyzer", tokenizer: "ngram", filters: ["lowercase", "trigrams_filter"])]
#[Analyzer(name: "standard", tokenizer: "ngram", filters: ["lowercase", "trigrams_filter"])]
#[NgramTokenizer(name: "ngram", token_chars: [TokenChars::DIGIT])]
#[NgramAbstractFilter(name: "trigrams_filter", min_gram: 3, max_gram: 3)]
abstract class AbstractGenerateProduct
{
    #[TextType]
    protected string $pk;

    #[IntegerType]
    protected int $parameterValues;

    #[IntegerType]
    protected int $parameters;

    #[KeywordType]
    protected string $productTags;

    #[KeywordType]
    protected ?string $description = null;

    /** @var \Doctrine\Common\Collections\ArrayCollection<Translations> */
    #[NestedType(properties: [
        new FloatType(name: "@cs"),
        new FloatType(name: "@en"),
        new FloatType(name: "@sk"),
    ])]
    protected ArrayCollection $sellingPrice;

    /** @var \Doctrine\Common\Collections\ArrayCollection<Translations> */
    #[ObjectType(properties: [
        new FloatType(name: "@cs"),
        new FloatType(name: "@en"),
        new FloatType(name: "@sk")
    ])]
    #[KeywordType(copy_to: "copy", name: "sellingPriceWithVatKeyword")]
    protected ArrayCollection $sellingPriceWithVat;

    #[MatchOnlyTextType(
        copy_to: "copy_match",
        fields: [new TextType(name: "extra_field")],
        meta: new Metadata(unit: "test_unit", metric_type: "test_metric")
    )]
    protected $matchOnlyText;

    #[ObjectType(properties: [
        new FloatType(name: "@cs"),
        new FloatType(name: "@en"),
        new FloatType(name: "@sk")
    ], name: "test1")]
    #[ObjectType(properties: [
        new FloatType(name: "@cs"),
        new FloatType(name: "@en"),
        new FloatType(name: "@sk")
    ], name: "test2")]
    #[ObjectType(keyResolver: true, properties: [
        new ObjectType(properties: [
            new ObjectType(properties: [
                new FloatType(name: "@en"),
                new FloatType(name: "@sk"),
            ], name: "second")
        ])
    ], name: "test3")]
    #[ObjectType(properties: [
        new ObjectType(properties: [
            new FloatType(name: "@en"),
            new FloatType(name: "@sk")
        ], name: "second")
    ], name: "test4")]
    #[ObjectType(
        keyResolver: true,
        fieldsTemplate: new TextType(analyzer: 'standard', name: 'name', fields: [
            new KeywordType(name: 'sort_name'),
            new TextType(analyzer: 'autocomplete_analyzer', name: 'autocomplete'),
        ]),
        name: "test5"
    )]
    protected ArrayCollection $translations;

    public function __construct()
    {
        $this->sellingPrice = new ArrayCollection();
        $this->sellingPriceWithVat = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    public function getPk(): string
    {
        return $this->pk;
    }

    public function setPk(string $pk): void
    {
        $this->pk = $pk;
    }

    public function getParameterValues(): int
    {
        return $this->parameterValues;
    }

    public function setParameterValues(int $parameterValues): void
    {
        $this->parameterValues = $parameterValues;
    }

    public function getParameters(): int
    {
        return $this->parameters;
    }

    public function setParameters(int $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function getProductTags(): string
    {
        return $this->productTags;
    }

    public function setProductTags(string $productTags): void
    {
        $this->productTags = $productTags;
    }

    public function getSellingPrice(): ArrayCollection
    {
        return $this->sellingPrice;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function addSellingPrice(Translations $sellingPrice): void
    {
        $this->sellingPrice[] = $sellingPrice;
    }

    public function getSellingPriceWithVat(): ArrayCollection
    {
        return $this->sellingPriceWithVat;
    }

    public function addSellingPriceWithVat(Translations $sellingPriceWithVat): void
    {
        $this->sellingPriceWithVat[] = $sellingPriceWithVat;
    }

    public function addTranslations(Translations $translations): void
    {
        $this->translations[] = $translations;
    }

    public function getTranslations(): ArrayCollection
    {
        return $this->translations;
    }

    /**
     * @return mixed
     */
    public function getMatchOnlyText()
    {
        return $this->matchOnlyText;
    }

    /**
     * @param mixed $matchOnlyText
     */
    public function setMatchOnlyText($matchOnlyText): void
    {
        $this->matchOnlyText = $matchOnlyText;
    }
}
