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

#[Index(name: "ProductModule")]
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

### Typy properties v JSON driveru

JSON driver rozezná tyhle typy; u ostatních property tiše přeskočí:

| ES typ | Třída |
|---|---|
| `text`, `match_only_text` | `TextType`, `MatchOnlyTextType` |
| `keyword`, `constant_keyword`, `wildcard` | `KeywordType`, `ConstantKeywordType`, `WildcardType` |
| `integer`, `long`, `short`, `byte`, `unsigned_long` | `IntegerType`, `LongType`, … |
| `float`, `double`, `half_float`, `scaled_float` | `FloatType`, `DoubleType`, … |
| `boolean` | `BooleanType` |
| `date`, `date_nanos` | `DateType`, `DateNanoType` |
| `binary`, `alias` | `BinaryType`, `AliasType` |
| `completion` | `CompletionType` |
| `rank_feature` | `RankFeatureType` |
| `geo_point` | `GeoPointType` |
| `icu_collation_keyword` | `IcuCollationKeywordType` (vyžaduje plugin `analysis-icu`) |
| `object`, `nested` | podle přítomnosti `properties` |

`scaled_float` vyžaduje `scaling_factor` a `alias` vyžaduje `path` — bez nich factory vyhodí
`AttributeMissingException` ještě před odesláním, protože by je odmítl i Elasticsearch.

`DateType` i `DateNanoType` umí `format`, `locale`, `null_value`, `index`, `doc_values`,
`store` a `ignore_malformed`:

```php
#[DateType(name: 'createdAt', format: 'yyyy-MM-dd', ignore_malformed: true)]
protected string $createdAt;
```

### Typy pro vyhledávací funkce

Tyhle typy potřebují konkrétní funkce z části Search — bez nich by šel postavit dotaz,
ale ne pole, nad kterým běží:

```php
// autocomplete pres CompletionSuggest
#[CompletionType(name: 'doplneni', max_input_length: 30)]
protected array $suggest;

// skorovani pres RankFeatureQuery; positive_score_impact: false pro veliciny,
// kde vyssi hodnota ma skore snizovat (napr. doba doruceni)
#[RankFeatureType(name: 'popularita')]
protected int $popularity;

// DistanceFeatureQuery, decay funkce s geo originem, GeoDistanceSort
#[GeoPointType(name: 'pozice', ignore_malformed: true)]
protected array $location;
```

`IcuCollationKeywordType` řeší jazykově správné řazení — v češtině se tak „Č" seřadí
za „C" a ne až za „Z". Typicky jako podpole jen pro řazení:

```php
#[TextType(name: 'nazev', fields: [
    new IcuCollationKeywordType(language: 'cs', country: 'CZ', index: false, name: 'razeni'),
])]
protected string $name;
```

Bez pluginu `analysis-icu` Elasticsearch odmítne index vytvořit hláškou
„No mapper found for type [icu_collation_keyword]".

### Nastavení indexu

Atribut `Index` kromě jména a `postEventClass` nese i nastavení indexu:

```php
#[Index(
    name: "ProductModule",
    max_result_window: 50000,
    number_of_shards: 2,
    number_of_replicas: 1,
    refresh_interval: "30s",
    max_ngram_diff: 5,
    max_shingle_diff: 4,
    dynamic: Dynamic::FALSE
)]
```

Do mappingu jde jen to, co je vyplněné (`max_result_window` má default 10000, `dynamic`
se posílá jen když není `Dynamic::TRUE`). Ke všemu
existují i settery, takže se to dá dolaďovat v post-event handleru — například vypnout
refresh pro hromadnou reindexaci:

```php
$index->setRefreshInterval('-1');
```

| Nastavení | K čemu |
|---|---|
| `max_result_window` | strop pro `from` + `size` (ES default 10000) |
| `number_of_shards` / `number_of_replicas` | rozložení indexu; shardy nelze u existujícího indexu měnit |
| `refresh_interval` | jak často se změny stanou viditelné; `-1` vypne |
| `max_ngram_diff` | povolený rozdíl `max_gram` − `min_gram` (ES default 1) |
| `max_shingle_diff` | povolený rozdíl u `ShingleFilter` (ES default 3) |
| `dynamic` | kořenové `mappings.dynamic` — `Dynamic::FALSE` nemapovaná pole jen uloží do `_source`, `Dynamic::STRICT` dokument s nimi odmítne |

Bez `max_ngram_diff` Elasticsearch odmítne vytvořit index s širším ngram rozsahem než 1 —
je to nejčastější důvod, proč `NgramTokenizer(min_gram: 2, max_gram: 6)` selže.

⚠️ `dynamic` je součást mappingu, u existujícího indexu ho nelze změnit — index je potřeba
vytvořit znovu a přeindexovat.

V JSON driveru se čte jak vnořená forma `settings.index.*`, tak plochá `settings.*` —
Elasticsearch bere obě; `mappings.dynamic` se čte jako string i boolean.

### Limity mappingu (`index.mapping.*`)

Limity mappingu mají vlastní class-level atribut `MappingSettings`, vedle `Index`:

```php
use Elasticsearch\Mapping\Settings\MappingSettings;

#[Index(name: "ProductModule", dynamic: Dynamic::FALSE)]
#[MappingSettings(total_fields_limit: 2000, nested_objects_limit: 20000)]
class ProductModule
```

| Parametr | ES klíč `index.mapping.` | od ES | K čemu |
|---|---|---|---|
| `total_fields_limit` | `total_fields.limit` | 8.0 | strop počtu mapperů — pole, objekty, multi-fields, aliasy, runtime pole (ES default 1000) |
| `total_fields_ignore_dynamic_beyond_limit` | `total_fields.ignore_dynamic_beyond_limit` | 8.13 | dynamická pole nad limit skončí v `_ignored` místo chyby |
| `depth_limit` | `depth.limit` | 8.0 | max. hloubka vnořených objektů (ES default 20) |
| `nested_fields_limit` | `nested_fields.limit` | 8.0 | max. počet různých `nested` mappingů (ES default 50 v 8.x, 100 v 9.x) |
| `nested_parents_limit` | `nested_parents.limit` | 9.0 | max. `nested` polí, která jsou rodičem dalších `nested` (ES default 50) |
| `nested_objects_limit` | `nested_objects.limit` | 8.0 | max. `nested` objektů v jednom dokumentu (ES default 10000) |
| `field_name_length_limit` | `field_name_length.limit` | 8.0 | max. délka názvu pole (ES default bez limitu) |
| `field_name_length_ignore_dynamic_beyond_limit` | `field_name_length.ignore_dynamic_beyond_limit` | 9.3 | příliš dlouhé dynamické názvy skončí v `_ignored` |
| `dimension_fields_limit` | `dimension_fields.limit` | 8.0 | max. dimenzí time series (TSDS, ES default 32768) |

Posílá se jen to, co je vyplněné. ⚠️ Elasticsearch odmítne vytvořit index s nastavením,
které jeho verze nezná. Knihovna podporuje ES 8.0 až 9.3, u každého klíče proto nese verzi,
od které existuje (`MappingSettings::PATHS`), a umí nastavení zkontrolovat proti verzi serveru:

```php
$unsupported = $index->getMappingSettings()?->unsupportedIn($serverVersion) ?? [];
// ['field_name_length.ignore_dynamic_beyond_limit' => '9.3.0'] => tohle ES 9.2 odmítne
```

Nastavení z novějších verzí (např. `array_objects.limit` z ES 9.5) záměrně chybí.

Limit polí se nejčastěji přeteče dynamickým mapováním — třeba objekt klíčovaný jazykem
nebo parametry produktu, kde každý nový klíč přidá pole. Buď limit zvedni, nebo dynamické
mapování vypni (`dynamic: Dynamic::FALSE`) a nemapovaná data nech jen v `_source`.

Hodnoty jdou měnit i za běhu, třeba v post-event handleru:

```php
$index->getMappingSettings()?->setTotalFieldsLimit(3000);
$index->setMappingSettings(new MappingSettings(depth_limit: 5)); // když atribut chybí
```

JSON driver čte `index.mapping.*` vnořeně (`settings.index.mapping.total_fields.limit`,
tak to vrací `GET _settings`) i jako tečkovaný klíč (`"index.mapping.total_fields.limit"`
nebo bez prefixu `index.`).

### Pole jen v `_source` (`SourceOnly`)

Někdy je potřeba mít data v dokumentu (vrací se v `_source` hitu), ale neindexovat je —
nemají se podle nich hledat, nemají zabírat místo v mappingu ani se počítat do
`total_fields.limit`. K tomu slouží atribut `SourceOnly`:

```php
use Elasticsearch\Mapping\Types\SourceOnly;

#[Index(name: "Article", dynamic: Dynamic::FALSE)]
class Article
{
    #[TextType]
    private string $title;

    #[SourceOnly]
    private string $teaser;                 // skalár

    #[SourceOnly(name: "meta")]
    private array $metadata;                // i pole — v dokumentu pod klíčem "meta"

    #[ObjectType(mappedBy: Comment::class)]
    private array $comments;
}

class Comment
{
    #[KeywordType(context: Article::class)]
    private string $author;

    #[SourceOnly(context: Article::class)]  // funguje i uvnitř mappedBy objektu
    private string $body;
}
```

Do mappingu se `SourceOnly` pole nedostane (`MetadataRequestFactory` i `ObjectType` ho
přeskočí), do dokumentu ho vloží `DefaultDocumentBuilder`, `ScalarValueResolverTrait`
i `CollectionByMappingResolverTrait` (viz [Indexing](../Indexing/README.md)).

⚠️ **Úroveň, na které `SourceOnly` leží, musí mít `dynamic: false`** — jinak by si
Elasticsearch pole při prvním dokumentu namapoval sám (`true`), nebo dokument odmítl
(`strict`). Nastavuje se na `#[Index(dynamic: Dynamic::FALSE)]` nebo na rodičovském
`ObjectType(dynamic: Dynamic::FALSE)`; `ObjectType` bez vlastního `dynamic` dědí hodnotu
rodiče. Při generování mappingu to knihovna kontroluje a vyhodí
`SourceOnlyRequiresDynamicFalseException` (potomek `MappingJsonCreateException`).

Jméno `SourceOnly` pole nesmí kolidovat s mapovaným polem (`DuplicityPropertyException`).

### Vestavěné analyzery

Vedle vlastního (custom) analyzeru skládaného z tokenizeru a filtrů (`Analyzer`) lze použít
analyzery, které má Elasticsearch zabudované. Definují se atributem na úrovni třídy stejně
jako `Analyzer` a nemají tokenizer ani filtry — jazykový analyzer už v sobě má tokenizer,
lowercase, stopwords i stemmer pro daný jazyk.

| Atribut | ES typ | Parametry |
|---|---|---|
| `LanguageAnalyzer(name, language)` | `czech`, `english`, … | `stopwords`, `stopwords_path`, `stem_exclusion` |
| `StandardAnalyzer(name)` | `standard` | `max_token_length`, `stopwords`, `stopwords_path` |
| `StopAnalyzer(name)` | `stop` | `stopwords`, `stopwords_path` |
| `PatternAnalyzer(name)` | `pattern` | `pattern`, `flags`, `lowercase`, `stopwords`, `stopwords_path` |
| `FingerprintAnalyzer(name)` | `fingerprint` | `separator`, `max_output_size`, `stopwords`, `stopwords_path` |
| `KeywordAnalyzer(name)` | `keyword` | `buffer_size` |
| `SimpleAnalyzer(name)` | `simple` | — |
| `WhitespaceAnalyzer(name)` | `whitespace` | — |

`````
#[LanguageAnalyzer(
    name: "czech_fulltext",
    language: AnalyzerLanguage::CZECH,
    stopwords: "_czech_",
    stem_exclusion: ["akce"]
)]
`````

Jazyky jsou v enumu `Settings\Analyzers\Enums\AnalyzerLanguage` — je to užší seznam než
u stemmer filtru, protože ne pro každý jazyk má Elasticsearch hotový analyzer.

V JSON driveru se analyzer pozná podle klíče `type`: chybí-li, nebo je `custom`, jde o vlastní
analyzer skládaný z tokenizeru a filtrů; jinak se použije odpovídající vestavěný analyzer.
Nerozpoznaný `type` se stejně jako dřív zpracuje jako custom analyzer.

`````json
{
    "analyzer": {
        "czech_builtin": { "type": "czech", "stopwords": "_czech_" },
        "vlastni":       { "tokenizer": "keep_special_chars", "filter": ["lowercase"] }
    }
}
`````

### Tokenizery

Tokenizer (`analysis.tokenizer`) rozpadá text na tokeny; každý custom analyzer má právě jeden.

| Atribut | ES typ | Hlavní parametry |
|---|---|---|
| `StandardTokenizer(name)` | `standard` | `max_token_length` |
| `WhitespaceTokenizer(name)` | `whitespace` | `max_token_length` |
| `KeywordTokenizer(name)` | `keyword` | `buffer_size` |
| `LetterTokenizer(name)` | `letter` | — |
| `LowercaseTokenizer(name)` | `lowercase` | — |
| `UaxUrlEmailTokenizer(name)` | `uax_url_email` | `max_token_length` |
| `ClassicTokenizer(name)` | `classic` | `max_token_length` |
| `CharGroupTokenizer(name, tokenize_on_chars)` | `char_group` | `max_token_length` |
| `PathHierarchyTokenizer(name)` | `path_hierarchy` | `delimiter`, `replacement`, `buffer_size`, `reverse`, `skip` |
| `PatternTokenizer(name)` | `pattern` | `pattern`, `flags`, `group` |
| `NgramTokenizer` / `EdgeNgramTokenizer` | `ngram` / `edge_ngram` | `min_gram`, `max_gram`, `token_chars` |

`CharGroupTokenizer` je levnější alternativa `pattern` tokenizeru, když stačí vyjmenovat
oddělovače — v `tokenize_on_chars` můžou být jak jména tříd znaků (`whitespace`, `letter`,
`digit`, `punctuation`, `symbol`), tak konkrétní znaky:

`````
#[CharGroupTokenizer(name: "catnum_chars", tokenize_on_chars: ["whitespace", "-", "/"])]
`````

`PathHierarchyTokenizer` vyrobí token pro každou úroveň cesty, což se hodí na kategoriové
fasety (`elektro|mobily|kryty` → `elektro`, `elektro|mobily`, `elektro|mobily|kryty`):

`````
#[PathHierarchyTokenizer(name: "category_path", delimiter: "|")]
`````

### Normalizery

Normalizer (`analysis.normalizer`) je obdoba analyzeru pro `keyword` pole — nemá tokenizer
a výsledkem je vždy jediný token. Používá se na sjednocení hodnot pro řazení a fasety.
Elasticsearch v něm připouští jen filtry, které nemění počet tokenů (`lowercase`,
`asciifolding`, `elision`, char filtry, …).

`````
#[Normalizer(name: "sort_normalizer", filters: ["lowercase", "asciifolding"])]
`````

Na pole se zapojí jménem přes `KeywordType`:

`````
#[KeywordType(normalizer: "sort_normalizer")]
protected string $sortName;
`````

`"Nové Boty"` pak v indexu leží jako jediný token `"nove boty"`. Ověřit to lze přes
`_analyze` s parametrem `normalizer` (viz níže).

### Token filtry

Token filtry (`analysis.filter`) pracují na už rozpadaných tokenech. Definují se atributem
na úrovni třídy a analyzer se na ně odkazuje jménem přes `filters` — **záleží na pořadí**,
filtry se aplikují zleva doprava.

| Atribut | ES typ | Hlavní parametry |
|---|---|---|
| `LowercaseFilter(name)` | `lowercase` | `language` (enum `LowercaseLanguage`: greek, irish, turkish) |
| `AsciiFoldingFilter(name)` | `asciifolding` | `preserve_original` |
| `SynonymFilter(name)` | `synonym` | `synonyms`, `synonyms_path`, `synonyms_set`, `expand`, `lenient`, `format`, `updateable` |
| `SynonymGraphFilter(name)` | `synonym_graph` | totéž; zvládá víceslovná synonyma, ale jen jako search analyzer |
| `WordDelimiterGraphFilter(name)` | `word_delimiter_graph` | `preserve_original`, `catenate_*`, `split_on_case_change`, `split_on_numerics`, `protected_words`, … |
| `ShingleFilter(name)` | `shingle` | `min_shingle_size`, `max_shingle_size`, `output_unigrams`, `token_separator`, `filler_token` |
| `ElisionFilter(name)` | `elision` | `articles`, `articles_path`, `articles_case` |
| `KeywordMarkerFilter(name)` | `keyword_marker` | `keywords`, `keywords_path`, `keywords_pattern`, `ignore_case` |
| `PatternReplaceFilter(name, pattern)` | `pattern_replace` | `replacement`, `all` |
| `LengthFilter(name)` | `length` | `min`, `max` |
| `UniqueFilter(name)` | `unique` | `only_on_same_position` |
| `TrimFilter(name)` | `trim` | — |
| `StopFilter(name, stopwords)` | `stop` | `stopwords_path`, `ignore_case`, `remove_trailing` |
| `StemmerFilter(name, language)` | `stemmer` | — |
| `NgramFilter` / `EdgeNgramFilter` | `ngram` / `edge_ngram` | `min_gram`, `max_gram` |
| `HunspellFilter(name, locale)` | `hunspell` | `dictionary`, `dedup`, `longest_only` |

`````
#[Analyzer(
    name: "fulltext",
    tokenizer: "standard",
    filters: ["czech_lowercase", "protect_brands", "czech_stemmer", "ascii"]
)]
#[LowercaseFilter(name: "czech_lowercase")]
#[KeywordMarkerFilter(name: "protect_brands", keywords: ["akce"], ignore_case: true)]
#[StemmerFilter(name: "czech_stemmer", language: Language::CZECH)]
#[AsciiFoldingFilter(name: "ascii", preserve_original: true)]
`````

Do mappingu jde jen to, co se liší od defaultu Elasticsearche.

U synonym si dej pozor: `expand: true` (default) doplní ke vstupu všechny varianty
(`laptop` → `laptop`, `notebook`), `expand: false` je naopak sjednotí na první term
(`laptop` → `notebook`). `KeywordMarkerFilter` musí být v pořadí filtrů **před** stemmerem,
jinak nemá co chránit. U `ShingleFilter` je potřeba myslet na `index.max_shingle_diff`
(default 3), u ngramů na `index.max_ngram_diff` (default 1).

### Ladění analyzerů (`_analyze`)

`Connection::analyze()` ukáže, na jaké tokeny Elasticsearch rozpadne zadaný text.
Bez indexu lze zkoušet jen vestavěné analyzery, s indexem i ty z jeho mappingu.

`````php
// analyzer z indexu
$result = $connection->analyze(
    new AnalyzeRequest('Nové knihy a akce v prodeji', analyzer: 'czech_fulltext'),
    $index
);
$result->getTokenValues();  // ['knih', 'akce', 'prodj']

// ad-hoc složení; filtr lze zadat i inline definicí, takže se dá otestovat
// dřív, než se dostane do indexu
$connection->analyze(new AnalyzeRequest(
    '<b>12.34</b>',
    tokenizer: 'whitespace',
    charFilter: ['html_strip'],
    filter: [['type' => 'stemmer', 'language' => 'danish']]
));
`````

Při `explain: true` vrací Elasticsearch místo `tokens` podrobný rozpad po jednotlivých krocích —
je pak v `AnalyzeResult::getDetail()`, ne v `getTokens()`.

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
