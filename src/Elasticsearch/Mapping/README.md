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
use Elasticsearch\Mapping\Settings\CharacterFilters\PatternReplaceCharacterFilter;
use Elasticsearch\Mapping\Settings\Filters\NgramFilter;
use Elasticsearch\Mapping\Types\Common\Numeric\IntegerType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;
use Elasticsearch\Mapping\Types\Text\TextType;

#[Index(name: "AmproductsModule")]
#[Analyzer(
    name: "trigrams",
    tokenizer: "ngram",
    filters: ["lowercase", "trigrams_filter"],
    charFilters: ["dots_replace_filter"]
)]
#[NgramTokenizer(name: "ngram", token_chars: [TokenChars::DIGIT])]
#[NgramAbstractFilter(name: "trigrams_filter", min_gram: 3, max_gram: 3)]
#[PatternReplaceCharacterFilter(name: "dots_replace_filter", pattern: "\.", replacement: "")]
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

### Character filtry

Character filtry (`analysis.char_filter`) upravují text ještě před tokenizací.
Definují se atributem na úrovni třídy a analyzer se na ně odkazuje jménem přes `charFilters`.
Atributy jsou repeatable, takže lze definovat víc char filtrů na jedné entitě.

Dostupné:

| Atribut | ES typ |
|---|---|
| `PatternReplaceCharacterFilter(name, pattern, replacement)` | `pattern_replace` |
| `MappingCharacterFilter(name, mappings)` nebo `MappingCharacterFilter(name, mappings_path: ...)` | `mapping` |
| `HtmlStripCharacterFilter(name, escaped_tags)` | `html_strip` |

`````
#[Analyzer(name: "catnum", tokenizer: "whitespace", filters: ["lowercase"], charFilters: ["dots_replace_filter"])]
#[PatternReplaceCharacterFilter(name: "dots_replace_filter", pattern: "\.", replacement: "")]
`````

U `PatternReplaceCharacterFilter` lze přidat regexp flagy (enum `Flags`, hodnoty odpovídají
`java.util.regex.Pattern`); ve vygenerovaném mappingu se spojí do `flags: "A|B"`:

`````
$filter = new PatternReplaceCharacterFilter('dots_replace_filter', '\.', '');
$filter->addFlag(Flags::CASE_INSENSITIVE)->addFlag(Flags::DOTALL);
`````

V JSON driveru se čte `settings.analysis.char_filter` a odkaz z analyzeru
(`analyzer.<name>.char_filter`) může být jak jedno jméno, tak pole jmen:

`````json
{
    "analyzer": {
        "whitespace_without_dots": {
            "tokenizer": "whitespace",
            "char_filter": ["dots_replace_filter"]
        }
    },
    "char_filter": {
        "dots_replace_filter": {
            "type": "pattern_replace",
            "pattern": "\\.",
            "replacement": ""
        }
    }
}
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
