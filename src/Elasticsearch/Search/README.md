# Elasticsearch - Search

Pro vyhledávání slouží továrna SearchBuilderFactory a její metoda create, která přijímá full class name.

```php
$builder = $searchBuilderFactory->create(Product::class);
$builder->setQuery(new TermQuery('field', 'value'));
$builder->addAggregation(new SumAggregation('sum', 'sellingPrice.@cs'));
$builder->addSort(new Sort('parameters', Sort::ASC));
$client->search($builder);
```

## Přidání query

Přes metodu `$builder->setQuery(...)` vložím objekt typu `Query` do builderu. Dostupné query jsou ve složce `src/Queries`.

Dostupné následující Query:

#### `ExistsQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-exists-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-exists-query.html)

```php
new \Elasticsearch\Search\Queries\ExistsQuery('terms_and_conditions');
```

#### `MatchQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html)

```php
new \Elasticsearch\Search\Queries\MatchQuery('name', 'john doe', 2);
```

#### `MultiMatchQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-multi-match-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-multi-match-query.html)

```php
new \Elasticsearch\Search\Queries\MultiMatchQuery('john', ['email', 'email'], 'auto');
```

#### `NestedQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-nested-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-nested-query.html)

```php
new \Elasticsearch\Search\Queries\NestedQuery(
    'user', 
    new \Spatie\ElasticsearchQueryBuilder\Queries\MatchQuery('name', 'john')
);
```

#### `RangeQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html)

```php
new \Elasticsearch\Search\Queries\RangeQuery('age')
    ->gte(18)
    ->lte(1337);
```

#### `TermQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html)

```php
new \Elasticsearch\Search\Queries\TermQuery('user.id', 'flx');
```

#### `WildcardQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-wildcard-query.html](https://www. elastic.co/guide/en/elasticsearch/reference/current/query-dsl-wildcard-query.html)

```php
new \Elasticsearch\Search\Queries\WildcardQuery('user.id', '*doe');
```

#### `MatchPhraseQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query-phrase.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query-phrase.html)

Slova v přesném pořadí vedle sebe; `slop` povoluje, kolik pozic smí být mezi nimi.

```php
new \Elasticsearch\Search\Queries\MatchPhraseQuery('name', 'černé boty', slop: 2);
```

#### `MatchPhrasePrefixQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query-phrase-prefix.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query-phrase-prefix.html)

Poslední slovo bere jako prefix — „search as you type" bez edge_ngram indexu.

```php
new \Elasticsearch\Search\Queries\MatchPhrasePrefixQuery('name', 'černé bo', max_expansions: 10);
```

#### `MatchBoolPrefixQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-bool-prefix-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-bool-prefix-query.html)

Na rozdíl od `match_phrase_prefix` nezáleží na pořadí slov — vhodné pro víceslovný autocomplete.

```php
new \Elasticsearch\Search\Queries\MatchBoolPrefixQuery('name', 'boty čer', operator: Operator::AND);
```

#### `SimpleQueryStringQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-simple-query-string-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-simple-query-string-query.html)

Na rozdíl od `query_string` nespadne na syntaktické chybě, jen nesmyslnou část ignoruje —
bezpečnější volba pro vstup od uživatele. `flags` omezí, které operátory smí uživatel použít.

```php
new \Elasticsearch\Search\Queries\SimpleQueryStringQuery(
    'boty -platěné',
    fields: ['name^3', 'description'],
    flags: [SimpleQueryStringFlag::AND, SimpleQueryStringFlag::NOT, SimpleQueryStringFlag::PHRASE],
    default_operator: Operator::AND
);
```

> Pozor: omezení `flags` mění chování parseru. Bez `WHITESPACE`/`ALL` se některé zápisy
> (např. `boty -platěné` s mezerou) neparsují tak, jak by člověk čekal. Je to vlastnost
> Elasticsearche, ne knihovny — ověřeno, že se stejným tělem vrací `curl` totéž.

#### `FuzzyQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html)

Tolerantní k překlepům. Pracuje na neanalyzované hodnotě, takže se hodí na `keyword` pole.

```php
new \Elasticsearch\Search\Queries\FuzzyQuery('code', 'ABC124', fuzziness: 'AUTO');
```

#### `RegexpQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html)

```php
new \Elasticsearch\Search\Queries\RegexpQuery('code', 'AB.*', flags: [RegexpFlag::COMPLEMENT]);
```

#### `TermsSetQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-set-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-set-query.html)

Vyžaduje jen zadaný minimální počet shod. Kolik jich musí být, se bere z pole dokumentu,
ze skriptu, nebo se zadá pevně — jedno z toho je povinné.

```php
new \Elasticsearch\Search\Queries\TermsSetQuery('tags', ['akce', 'novinka'], minimum_should_match_field: 'req');
```

#### `IdsQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-ids-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-ids-query.html)

```php
new \Elasticsearch\Search\Queries\IdsQuery(['1', '2']);
```

### Ovlivnění relevance

Tyhle query žijí v podsložkách podle kategorií, které používá dokumentace Elasticsearche:

| Namespace | Query |
|---|---|
| `Search\Queries\Compound` | `BoostingQuery`, `ConstantScoreQuery`, `FunctionScoreQuery` |
| `Search\Queries\Compound\FunctionScore` | funkce pro `FunctionScoreQuery` |
| `Search\Queries\Specialized` | `DistanceFeatureQuery`, `MoreLikeThisQuery`, `PinnedQuery`, `RankFeatureQuery`, `ScriptScoreQuery` |
| `Search\Queries\Specialized\RankFeature` | funkce pro `RankFeatureQuery` |

Ostatní query zatím zůstávají přímo v `Search\Queries` — přesunou se až v samostatném kroku,
protože u nich by to byl BC break.

#### `ConstantScoreQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-constant-score-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-constant-score-query.html)

Obalí filtr a všem shodám dá stejné skóre — ES nemusí počítat relevanci a může výsledek cachovat.

```php
new \Elasticsearch\Search\Queries\ConstantScoreQuery(new TermQuery('inStock', true));
```

#### `BoostingQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-boosting-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-boosting-query.html)

Dokumenty odpovídající `negative` nevyřadí, jen jim sníží skóre — pro věci, které nechceme
skrýt, jen odsunout dozadu (nedostupné produkty apod.).

```php
new \Elasticsearch\Search\Queries\BoostingQuery(
    positive: new MatchQuery('name', 'boty'),
    negative: new TermQuery('sellingDenied', true),
    negative_boost: 0.1
);
```

#### `ScriptScoreQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-script-score-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-script-score-query.html)

```php
new \Elasticsearch\Search\Queries\ScriptScoreQuery(
    new MatchAllQuery(),
    ['source' => "doc['popularity'].value * params.factor", 'params' => ['factor' => 2]]
);
```

#### `DistanceFeatureQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-distance-feature-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-distance-feature-query.html)

Zvyšuje skóre podle blízkosti k bodu — novinky dřív, bližší prodejna dřív. Pole musí být
`date`, `date_nanos` nebo `geo_point`.

```php
new \Elasticsearch\Search\Queries\DistanceFeatureQuery('createdAt', 'now', '7d');
new \Elasticsearch\Search\Queries\DistanceFeatureQuery('location', [14.42, 50.08], '10km');
```

#### `RankFeatureQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-rank-feature-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-rank-feature-query.html)

Zvyšuje skóre podle hodnoty v poli typu `rank_feature` (popularita, počet prodejů).
Funkce jsou v `Search\Queries\Functions`:

| Funkce | Chování |
|---|---|
| `SaturationFunction(?pivot)` | skóre roste a nasytí se; u `pivot` je 0.5. Bez pivotu si ho ES spočítá z dat |
| `LogarithmFunction(scaling_factor)` | `log(scaling_factor + hodnota)` |
| `SigmoidFunction(pivot, exponent)` | jako saturation, `exponent` řídí strmost |
| `LinearFunction()` | skóre přímo proporční hodnotě |

```php
new \Elasticsearch\Search\Queries\RankFeatureQuery('popularity', new SaturationFunction(50.0));
```

#### `FunctionScoreQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html)

Přepočítá skóre jednou nebo více funkcemi. `score_mode` říká, jak se složí funkce mezi sebou,
`boost_mode` jak se výsledek spojí se skóre z původní query.

Funkce jsou v `Search\Queries\Compound\FunctionScore`; každá může mít vlastní `filter`
(uplatní se jen na jeho shody) a `weight`:

| Funkce | Chování |
|---|---|
| `WeightFunction(weight, ?filter)` | samotná váha — typicky „co odpovídá filtru, násob skóre X" |
| `FieldValueFactorFunction(field, ?factor, ?modifier, ?missing)` | skóre z hodnoty číselného pole |
| `RandomScoreFunction(?seed, ?field)` | náhodné skóre; se `seed` stabilní mezi dotazy |
| `ScriptScoreFunction(script)` | skóre ze skriptu |
| `GaussDecayFunction(field, origin, scale, ?offset, ?decay, ?multi_value_mode)` | zvonová křivka |
| `ExpDecayFunction(…)` | exponenciální pokles |
| `LinearDecayFunction(…)` | lineární pokles, dojde na nulu |

```php
$query = new FunctionScoreQuery(
    new MatchQuery('name', 'boty'),
    [
        new WeightFunction(3.0, new TermQuery('inStock', true)),
        new FieldValueFactorFunction('popularity', factor: 1.2, modifier: FieldValueFactorModifier::SQRT, missing: 1.0),
        new GaussDecayFunction('createdAt', 'now', '10d', decay: 0.5),
    ],
    score_mode: ScoreMode::SUM,
    boost_mode: BoostMode::MULTIPLY,
    max_boost: 10.0
);
$query->addFunction(new RandomScoreFunction(seed: 10, field: '_seq_no'));
```

Na co narazit:

- U `FieldValueFactorFunction` se vyplatí zadat `missing` — bez ní skončí dokumenty
  bez toho pole chybou.
- `LinearDecayFunction` na rozdíl od gauss a exp dojde na nulu, takže vzdálenější
  dokumenty dostanou skóre 0 a s `min_score` úplně vypadnou.
- `multi_value_mode` je v JSONu sourozenec pole, ne jeho součást — knihovna to řeší za tebe.
- `LinearDecayFunction` (decay) je něco jiného než `RankFeature\LinearFunction`
  (pro `rank_feature`), i když se obě v JSONu jmenují `linear`.

#### `PinnedQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-pinned-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-pinned-query.html)

Vybrané dokumenty vytáhne na začátek, zbytek dohledá `organic`. Zadává se buď `ids`,
nebo `docs` (když je potřeba i index) — ne obojí.

```php
new \Elasticsearch\Search\Queries\PinnedQuery(new MatchQuery('name', 'boty'), ids: ['1', '2']);
```

#### `MoreLikeThisQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-mlt-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-mlt-query.html)

„Podobné produkty" — v `like` může být text i odkaz na dokument.

```php
new \Elasticsearch\Search\Queries\MoreLikeThisQuery(
    ['name', 'description'],
    [['_index' => 'product', '_id' => '1']],
    min_term_freq: 1,
    min_doc_freq: 1
);
```

#### `BoolQuery`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html)

```php
(new \Elasticsearch\Search\Queries\BoolQuery())
    ->add(new \Elasticsearch\Search\Queries\MatchQuery('test', 'test'), 'must_not')
    ->add(new \Elasticsearch\Search\Queries\ExistsQuery('test'), 'must_not');
```

More information on the boolean query and its occurrence types can be found [in the ElasticSearch docs](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html).

## Přidání agregace

Přes `$builder->addAggregation()` metodu můžeme přidat agregační pravidlo rozhranní `Aggregation` do builderu. Dostupné agregace jsou ve složce `src/Aggregations`.

```php
$builder->addAggregation(new \Elasticsearch\Search\Aggregations\SumAggregation('sum', 'sellingPrice.@cs'));
```

Dostupné jsou následující agregace:

#### `CardinalityAggregation`

```php
new \Elasticsearch\Search\Aggregations\CardinalityAggregation('team_agg', 'team_name');
```

#### `FilterAggregation`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-filter-aggregation.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-filter-aggregation.html)

```php
new \Elasticsearch\Search\Aggregations\FilterAggregation(
    'tshirts',
    new \Elasticsearch\Search\Queries\TermQuery('type', 'tshirt'),
    new \Elasticsearch\Search\Aggregations\MaxAggregation('max_price', 'price')
);
```

#### `MaxAggregation`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-max-aggregation.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-max-aggregation.html)

```php
new \Elasticsearch\Search\Aggregations\MaxAggregation('max_price', 'price');
```

#### `MinAggregation`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-min-aggregation.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-min-aggregation.html)

```php
new \Elasticsearch\Search\Aggregations\MinAggregation('min_price', 'price');
```

#### `SumAggregation`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-sum-aggregation.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-sum-aggregation.html)

```php
new \Elasticsearch\Search\Aggregations\SumAggregation('sum_price', 'price');
```

#### `NestedAggregation`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-nested-aggregation.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-nested-aggregation.html)

```php
new \Elasticsearch\Search\Aggregations\NestedAggregation(
    'resellers',
    'resellers',
    new \Elasticsearch\Search\Aggregations\MinAggregation('min_price', 'resellers.price'),
    new \Elasticsearch\Search\Aggregations\MaxAggregation('max_price', 'resellers.price'),
);
```

#### `ReverseNestedAggregation`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-reverse-nested-aggregation.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-reverse-nested-aggregation.html)

```php
new \Elasticsearch\Search\Aggregations\ReverseNestedAggregation(
    'name',
    ...$aggregations
);
```

#### `TermsAggregation`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-terms-aggregation.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-terms-aggregation.html)

```php
new \Elasticsearch\Search\Aggregations\TermsAggregation(
    'genres',
    'genre'
)
    ->size(10)
    ->order(['_count' => 'asc'])
    ->missing('N/A')
    ->aggregation(/* $subAggregation */);
```

#### `TopHitsAggregation`

[https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-top-hits-aggregation.html](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-top-hits-aggregation.html)

```php
new \Elasticsearch\Search\Aggregations\TopHitsAggregation(
    'top_sales_hits',
    10,
);
```

## Metrické agregace

| Třída | ES agregace |
|---|---|
| `AvgAggregation(name, field)` | `avg` |
| `MinAggregation` / `MaxAggregation` / `SumAggregation` | `min` / `max` / `sum` |
| `ValueCountAggregation(name, field)` | `value_count` — počet hodnot (ne unikátních) |
| `CardinalityAggregation(name, field)` | `cardinality` — přibližný počet unikátních |
| `StatsAggregation(name, field)` | `stats` — count, min, max, avg, sum jedním dotazem |
| `ExtendedStatsAggregation(name, field)` | `extended_stats` — navíc rozptyl a odchylka, volitelně `sigma()` |
| `PercentilesAggregation(name, field)` | `percentiles` — `percents()`, `keyed()`, `compression()` |
| `PercentileRanksAggregation(name, field, values)` | `percentile_ranks` — obrácené percentily |
| `WeightedAvgAggregation(name, valueField, weightField)` | `weighted_avg` |

```php
$percentiles = new PercentilesAggregation('cenove_pasmo', 'price');
$percentiles->percents([50.0, 95.0]);
$builder->addAggregation($percentiles);

// hodnocení vážené počtem recenzí
$builder->addAggregation(new WeightedAvgAggregation('hodnoceni', 'rating', 'reviews'));
```

`missing()` (u většiny metrik) dosadí hodnotu za dokumenty, kterým pole chybí — bez toho
se do agregace vůbec nepočítají.

### `TermsAggregation`

Kromě `size()`, `order()` a `missing()` má i `shardSize()`, `minDocCount()`, `include()`
a `exclude()`:

```php
$terms = new TermsAggregation('znacky', 'brand');
$terms->size(10)
    ->minDocCount(2)          // vynechá značky s jedním produktem
    ->exclude('Gama.*')       // regulární výraz, nebo pole hodnot
    ->shardSize(100);         // přesnější doc_count při více shardech
```

## Bucket agregace

| Třída | ES agregace |
|---|---|
| `HistogramAggregation(name, field, interval)` | `histogram` — intervaly pevné šířky |
| `DateHistogramAggregation(name, field)` | `date_histogram` — `calendarInterval()` nebo `fixedInterval()` |
| `RangeAggregation(name, field, ...Range)` | `range` — vlastní intervaly |
| `DateRangeAggregation(name, field, ...Range)` | `date_range` — ve `from`/`to` i date math |
| `FiltersAggregation(name, [key => Query])` | `filters` — bucket za každou podmínku |
| `MissingAggregation(name, field)` | `missing` — dokumenty bez hodnoty |
| `MultiTermsAggregation(name, ...fields)` | `multi_terms` — kombinace hodnot více polí |
| `RareTermsAggregation(name, field)` | `rare_terms` — nejvzácnější termy |
| `SignificantTermsAggregation(name, field)` | `significant_terms` — neobvykle časté termy |
| `SamplerAggregation(name)` | `sampler` — omezí podagregace na vzorek |
| `CompositeAggregation(name, ...sources)` | `composite` — stránkovatelné |
| `TermsAggregation`, `FilterAggregation`, `NestedAggregation`, `GlobalAggregation` | beze změny |

Všechny umí podagregace — buď variadicky v konstruktoru, nebo přes `aggregation()`:

```php
$builder->addAggregation(
    new MissingAggregation('bez_ceny', 'price', new AvgAggregation('prumer', 'rating'))
);
```

### Intervaly

```php
$pasma = new HistogramAggregation('cenova_pasma', 'price', 100.0);
$pasma->minDocCount(0)->extendedBounds(0.0, 1000.0);   // i prázdné intervaly

$mesice = new DateHistogramAggregation('po_mesicich', 'createdAt');
$mesice->calendarInterval('month')->format('yyyy-MM')->timeZone('Europe/Prague');
```

`calendarInterval()` respektuje délku měsíce a letní čas, `fixedInterval()` je pevný úsek
(`"30d"`). Zadat lze právě jedno z nich — jinak agregace vyhodí výjimku ještě před odesláním.

### Rozsahy

```php
use Elasticsearch\Search\Aggregations\Range;

new RangeAggregation('pasma', 'price',
    new Range(to: 100, key: 'levne'),
    new Range(from: 100, to: 500),
    new Range(from: 500, key: 'drahe')
);
```

`from` je včetně, `to` už ne; vynechaná hranice znamená neomezeno.

### Stránkování přes `composite`

Jediná agregace, která umí projít **všechny** buckety. Odpověď vrací `after_key`, který se
předá do `after()` pro další stránku:

```php
use Elasticsearch\Search\Aggregations\Composite\{HistogramSource, TermsSource};

$after = null;
do {
    $agg = new CompositeAggregation('strany', new TermsSource('znacka', 'brand'));
    $agg->size(100);
    if ($after) {
        $agg->after($after);
    }
    $builder->addAggregation($agg);

    $result = $connection->search($builder)->getAggregations()->toArray();
    $after = $result['strany']['after_key'] ?? null;
} while ($after);
```

Zdroje jsou `TermsSource`, `HistogramSource` a `DateHistogramSource`; každý umí `order()`
a `missingBucket()` (bez něj dokumenty bez hodnoty z výsledku vypadnou).

### Na co narazit

- `SignificantTermsAggregation` porovnává četnost ve výsledku dotazu proti celému indexu —
  s `MatchAllQuery` je popředí i pozadí totéž a nevrátí nic. Má smysl jen nad zužujícím
  dotazem, případně s `backgroundFilter()`.
- `FiltersAggregation` (množné číslo) je něco jiného než `FilterAggregation` — první dělá
  bucket za každou podmínku, druhá filtruje jednou.
- `MultiTermsAggregation` je dražší než vnořené `terms`, ale nepřijde o kombinace, které by
  se ořezem `size` na vyšší úrovni ztratily.

## Přidání sorts

`Builder` má `addSort()` methodu s rozhranním `Sort`. Více v dokumentaci [the ElasticSearch docs](https://www.elastic.co/guide/en/elasticsearch/reference/current/sort-search-results.html).

```php
use Elasticsearch\Search\Sorts\Sort;

$builder
    ->addSort(new Sort('age', Sort::DESC))
    ->addSort(
        (new Sort('score', Sort::ASC))
            ->unmappedType('long')
            ->missing(0)
    );
```

##### Nested sorting
```php
$sort = new Sort('parameters', SortDirection::ASC);
$sort->setMode(SortMode::SUM);
$sort->setNestedSort(
    new NestedSort('sellingPrice', $query, new NestedSort('sellingPrice.@cz'))
);
$builder->addSort($sort);
```

Řazení podporuje Geo sorting (GeoDistanceSort.php) a Script sorting (ScriptSort.php).
https://www.elastic.co/docs/reference/elasticsearch/rest-apis/sort-search-results#geo-sorting
https://www.elastic.co/docs/reference/elasticsearch/rest-apis/sort-search-results#script-based-sorting

## Retrieve specific fields

The `fields()` method can be used to request specific fields from the resulting documents without returning the entire `_source` entry. You can read more about the specifics of the fields parameter in [the ElasticSearch docs](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-fields.html).

```php
$builder->fields('user.id', 'http.*.status');
```

## Pagination

`Builder` obsahuje `size()` a `from()` metody pro korespondují parametry ElasticSearch parametry, používané pro stránkování:

```php
use Elasticsearch\Search\Builder;

$pageSize = 100;
$pageNumber = $_GET['page'] ?? 1;

$query = (new Builder('class'))
    ->size($pageSize)
    ->from(($pageNumber - 1) * $pageSize)
    ->build();
```

## Highlight

`Builder::setHighlight()` přidá do těla requestu sekci `highlight`, takže Elasticsearch
u každého hitu vrátí úseky textu se zvýrazněnými shodami.

```php
use Elasticsearch\Search\Highlight\{Highlight, HighlightField};
use Elasticsearch\Search\Highlight\Enums\{BoundaryScanner, HighlightOrder};

$highlight = new Highlight(
    new HighlightField('name'),
    (new HighlightField('description'))->setNumberOfFragments(2)
);
$highlight->setTags(['<mark>'], ['</mark>'])
    ->setFragmentSize(40)
    ->setOrder(HighlightOrder::SCORE)
    ->setBoundaryScanner(BoundaryScanner::SENTENCE);

$builder->setHighlight($highlight);
```

Volby lze zadat globálně na `Highlight` i u jednotlivých `HighlightField` — u pole přebijí
tu globální. K dispozici jsou `type`, `pre_tags`/`post_tags`, `fragment_size`,
`number_of_fragments`, `order`, `require_field_match`, `boundary_scanner`,
`boundary_max_scan`, `boundary_chars`, `no_match_size`, `highlight_query`, `phrase_limit`,
`fragmenter`, `max_analyzed_offset`; navíc globálně `encoder` a `useStyledTags()`,
u pole `matched_fields` a `fragment_offset`.

Ve výsledku se k úsekům dostaneš přes `getHighlights()`, naklíčované podle `_id`:

```php
$result = $connection->search($builder);

foreach ($result->getHits()->getHighlights() as $id => $fields) {
    foreach ($fields['description'] ?? [] as $fragment) {
        echo $fragment;   // Kozene <mark>boty</mark> se hodi i do prace.
    }
}
```

Iterace `getHits()` zůstává na surových hitech, takže `$hit['highlight']` funguje dál —
`getHighlights()` je jen zkratka.

Na co narazit:

- `no_match_size` je jediný způsob, jak dostat obsah pole, ve kterém shoda není; bez něj
  se takové pole ve výsledku vůbec neobjeví.
- `matched_fields` funguje jen s `fvh` highlighterem a pole musí mít v mappingu
  `term_vector: with_positions_offsets`.
- `useStyledTags()` použije předpřipravené značky `<em class="hlt1">` až `<em class="hlt10">`.

## Suggest

`Builder::setSuggest()` přidá do těla requestu sekci `suggest`. Dostupné jsou tři suggestery:

| Třída | ES suggester | K čemu |
|---|---|---|
| `TermSuggest(name, text, field)` | `term` | opravy po jednotlivých slovech |
| `PhraseSuggest(name, text, field)` | `phrase` | oprava celé fráze, bere v potaz výskyt slov spolu |
| `CompletionSuggest(name, field, prefix:)` | `completion` | autocomplete nad polem typu `completion` |

```php
use Elasticsearch\Search\Suggest\{CompletionSuggest, Suggest, TermSuggest};
use Elasticsearch\Search\Suggest\Enums\SuggestMode;

$builder->setSuggest(new Suggest(
    new TermSuggest('opravy', 'boyt', 'name', suggest_mode: SuggestMode::ALWAYS, size: 3),
    new CompletionSuggest('doplneni', 'doplneni', prefix: 'cerne', skip_duplicates: true)
));
```

Ve výsledku jsou návrhy naklíčované jménem suggesteru:

```php
$result = $connection->search($builder);

foreach ($result->getSuggest('opravy') as $entry) {
    $entry->getText();          // 'boyt'  - vstupní úsek
    $entry->getOptionTexts();   // ['boty']
    $entry->getFirstOption()?->getScore();
}
```

`SuggestOption` nese `text` a `score`; `freq` vrací jen term suggester, `_id`/`_index`/`_source`
jen completion suggester.

Na co narazit:

- `CompletionSuggest` se zadává `prefix` nebo `regex`, ne `text` — a právě jedním z nich;
  jinak vyhodí výjimku ještě před odesláním.
- `PhraseSuggest` potřebuje pole analyzované shingle filtrem (typicky podpole
  `name.trigram`), jinak nemá z čeho fráze skládat.
- Query parametry `suggest_field`/`suggest_text`/`suggest_mode` v `SearchParams` zůstávají
  a fungují, ale umí jen term suggester — pro `phrase` a `completion` je potřeba tělo
  requestu. Suggester se u nich v odpovědi jmenuje podle **pole**, ne podle jména, které
  bys zvolil; `Result::getSuggests()` ho tedy najdeš pod klíčem `name`, ne `opravy`.
- `SuggestMode` je jeden enum (`Search\Suggest\Enums\SuggestMode`) pro tělo requestu
  i pro query parametr.

## Další volby těla requestu

```php
$builder->setPostFilter(new TermQuery('brand', 'Alfa'));   // filtr aplikovaný PO agregacích
$builder->minScore(1.0);                                    // zahodí hity pod skóre
$builder->trackTotalHits(true);                             // přesný počet (nebo limit: 10000)
$builder->trackScores(true);                                // skóre i při řazení podle pole
```

`post_filter` je klíčový pro fasety: agregace se počítají z výsledků **před** ním, takže
výběr jedné hodnoty nezúží nabídku ostatních.

```php
$builder->addScriptField('sleva', ['source' => "doc['price'].value * 0.8"]);
$builder->addRuntimeMapping('pasmo', [
    'type'   => 'keyword',
    'script' => ['source' => "emit(doc['price'].value > 150 ? 'drahe' : 'levne')"],
]);
```

`script_fields` se vrací v `$hit['fields']`, ne v `_source`. `runtime_mappings` definuje pole
počítané až při hledání — jde ho použít i v agregaci nebo řazení, aniž by se zapisovalo do indexu.

### Rescore

Přepočítá skóre jen u prvních `window_size` výsledků z každého shardu — pro drahé query,
které se nevyplatí pustit přes celý index.

```php
use Elasticsearch\Search\Rescore\{Enums\RescoreMode, Rescore};

$builder->addRescore(new Rescore(
    new MatchPhraseQuery('name', 'černé kožené'),
    window_size: 50,
    query_weight: 0.3,
    rescore_query_weight: 2.0,
    score_mode: RescoreMode::TOTAL
));
```

Jeden rescore se posílá jako objekt, víc jich jako pole — knihovna to řeší sama.

### Point in Time

Zamrznutý pohled na index, aby hluboké stránkování přes `search_after` vracelo konzistentní
výsledky, i když se mezitím indexuje.

```php
$pit = $connection->openPointInTime($index, '2m');

$after = null;
do {
    $builder = $searchBuilderFactory->create(Product::class);
    $builder->setQuery(new MatchAllQuery());
    $builder->addSort(new Sort('price', SortDirection::ASC));
    $builder->size(100);
    $builder->setPointInTime($pit);
    if ($after) {
        $builder->searchAfter($after);
    }

    $result = $connection->search($builder);
    $hits = $result->getHits()->toArray();
    $after = $hits ? end($hits)['sort'] : null;
} while ($hits);

$connection->closePointInTime($pit);
```

Na co narazit:

- S PIT se **index neposílá v requestu** — je součástí PIT. `Builder::build()` ho proto
  vynechá; s indexem by ES request odmítl.
- PIT je potřeba zavřít, jinak drží zdroje až do vypršení `keep_alive`.
- `keep_alive` je u otevření povinné, knihovna použije `1m`, když ho neuvedeš.

[]() > [Ukázka použítí](../../../examples/searchData.php) <

[<< zpět](../../../README.md)
