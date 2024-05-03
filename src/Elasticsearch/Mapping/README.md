# Elasticsearch - Mapping

Sestavení mappingu využívá předpřipravených tříd (DTO).
Pro načítání mappingu slouží třída MappingMetadataFactory, která přijímá driver (DriverInterface).

Momentálně jsou dostupné 2 drivery:
- attributes
- json

Je však celkem jednoduché si napsat vlastní driver a číst mapping třeba z yaml souboru.

### Ukázka mappingu přes atributy

`````
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Settings\Analyzer;
use Elasticsearch\Mapping\Settings\Filters\NgramFilter;
use Elasticsearch\Mapping\Types\Common\Numeric\IntegerType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;
use Elasticsearch\Mapping\Types\Text\TextType;

#[Index(name: "AmproductsModule")]
#[Analyzer(name: "trigrams", tokenizer: "ngram", filters: ["lowercase", "trigrams_filter"])]
#[NgramTokenizer(name: "ngram", token_chars: [TokenChars::DIGIT])]
#[NgramAbstractFilter(name: "trigrams_filter", min_gram: 3, max_gram: 3)]
abstract class AbstractGenerateProduct
{
    #[TextType]
    protected $pk;

    #[IntegerType]
    protected $parameterValues;

    #[IntegerType]
    protected int $parameters;

    #[KeywordType]
    protected string $productTags;

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
    #[KeywordType(name: "sellingPriceWithVatKeyword")]
    protected ArrayCollection $sellingPriceWithVat;
`````

ObjectType (NestedType) se používá hodně pro překladová pole a každý field má svůj index klíč (cs, en, atd.).
Proto je možné použít speciální syntax a vytvořit si Key resolver podle vlastní potřeby.

`````
    #[ObjectType(keyResolver: true, properties: [
        new ObjectType(properties: [
            new ObjectType(properties: [
                new FloatType(name: "@en"),
                new FloatType(name: "@sk"),
            ], name: "second")
        ])
    ], name: "test3")]
`````
KeyResolver musí implementovat rozhranní KeyresolverInterface a je potřeba ho zapojit do driveru.
`````
public function setKeyResolver(?KeyresolverInterface $keyResolver): void
`````

Pro relace je možné využít mapovaní (mappedBy a context):
`````
class Book
{
    /** @var Attachment[] */
    #[ObjectType(context: Author::class, mappedBy: Attachment::class)]
    private array $attachments;
}

class Attachment
{
    #[IntegerType(context: Book::class)]
    private int $id;

    #[KeywordType(context: Book::class)]
    private string $name;
}
`````

[]() > [Ukázka použítí](../../../examples/createIndex.php) <

[<< zpět](../../../README.md)
