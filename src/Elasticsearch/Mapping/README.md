# Elasticsearch - Mapping

Sestavení mappingu využívá předpřipravených tříd (DTO).
Pro načítání mappingu slouží třída MappingMetadataFactory, která přijímá driver (DriverInterface).

Momentálně je dostupný pouze jeden driver, který umí číst mapping z php anotací.
Je však celkem jednoduché si napsat vlastní driver a číst mapping třeba z yaml souboru.

### Ukázka anotací

`````
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Settings\Analyzer;
use Elasticsearch\Mapping\Settings\Filters\NgramFilter;
use Elasticsearch\Mapping\Types\Common\Numeric\IntegerType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;
use Elasticsearch\Mapping\Types\Text\TextType;

/**
 * @Index(
 *     name="AmproductsModule",
 *     analysis=@Analysis(
 *          analyzers={
 *              @Analyzer(name="trigrams", type="custom", tokenizer="standard", filters={"lowercase", "trigrams_filter"})
 *          },
 *          filters={
 *              @NgramFilter(name="trigrams_filter", type="ngram",min_gram=3, max_gram=3)
 *          }
 *     )
 * )
 */
abstract class AbstractGenerateProduct
{
    /**
     * @var string
     * @TextType()
     */
    protected $pk;

    /**
     * @var int
     * @IntegerType()
     */
    protected $parameterValues;

    /**
     * @var int
     * @IntegerType()
     */
    protected $parameters;
    
    /**
     * @var string
     * @ObjectType(name="test1", fields={
     *     @FloatType(name="@cs"),
     *     @FloatType(name="@en"),
     *     @FloatType(name="@sk")
     * })
     * @ObjectType(name="test2", fields={
     *     @FloatType(name="@cs"),
     *     @FloatType(name="@en"),
     *     @FloatType(name="@sk")
     * })
     */
    protected $translations;
`````

ObjectType se používá hodně pro překladová pole a každý field má svůj index klíč (cs, en, atd.).
Proto je možné použít speciální syntax a vytvořit si Key resolver podle vlastní potřeby.

`````
     * @ObjectType(
     *     name="test",
     *     keyResolver=true,
     *     fieldsTemplate=@TextType()
     * )
`````
KeyResolver musí implementovat rozhranní KeyresolverInterface a zapojuje se do driveru.
`````
public function setKeyResolver(?KeyresolverInterface $keyResolver): void
`````

[]() > [Ukázka použítí](../../../examples/createIndex.php) <

[<< zpět](../../../README.md)
