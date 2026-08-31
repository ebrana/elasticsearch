# Changelog

Všechny podstatné změny této knihovny. Formát vychází z [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
verzování ze [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v1.1.0]

### Přidáno

#### Mapping — analysis

- **Character filtry se dají reálně použít.** `Analyzer` má nový parametr `charFilters`,
  kterým se analyzer odkáže na `pattern_replace`, `mapping` nebo `html_strip` filtr.
  `AnnotationDriver` nově sbírá i `AbstractCharactedFilter` atributy a ty jsou repeatable,
  takže jich na entitě může být víc.
- **Vestavěné analyzery** místo dosud jediného custom analyzeru: `LanguageAnalyzer`
  (czech, english, …), `StandardAnalyzer`, `StopAnalyzer`, `PatternAnalyzer`,
  `FingerprintAnalyzer`, `KeywordAnalyzer`, `SimpleAnalyzer`, `WhitespaceAnalyzer`.
  Nové rozhraní `AnalyzerInterface` a `AbstractAnalyzer`, nový enum `AnalyzerLanguage`.
- **12 token filtrů**: `lowercase`, `asciifolding`, `synonym`, `synonym_graph`,
  `word_delimiter_graph`, `shingle`, `elision`, `keyword_marker`, `pattern_replace`,
  `length`, `unique`, `trim`. Enumy `LowercaseLanguage`, `SynonymFormat`.
- **8 tokenizerů**: `keyword`, `whitespace`, `letter`, `lowercase`, `uax_url_email`,
  `char_group`, `path_hierarchy`, `classic`.
- **Normalizery** (`analysis.normalizer`) — nová třída `Normalizer`, sběr atributů,
  čtení z JSONu i zápis do mappingu. `KeywordType(normalizer: …)` na ně už umělo odkazovat.
- `Connection::analyze()` pro ladění analyzerů (`_analyze` API) s `AnalyzeRequest`,
  `AnalyzeResult` a `AnalyzeToken`. Filtry lze zadat jménem i inline definicí.

#### Search — query

- Fulltextové query: `MatchPhraseQuery`, `MatchPhrasePrefixQuery`, `MatchBoolPrefixQuery`,
  `SimpleQueryStringQuery`, `FuzzyQuery`, `RegexpQuery`, `TermsSetQuery`, `IdsQuery`.
  Enumy `SimpleQueryStringFlag`, `RegexpFlag`.
- Query ovlivňující relevanci: `ConstantScoreQuery`, `BoostingQuery` (`Queries\Compound`),
  `ScriptScoreQuery`, `DistanceFeatureQuery`, `RankFeatureQuery`, `PinnedQuery`,
  `MoreLikeThisQuery` (`Queries\Specialized`).
- `FunctionScoreQuery` se scoring funkcemi `WeightFunction`, `FieldValueFactorFunction`,
  `RandomScoreFunction`, `ScriptScoreFunction`, `GaussDecayFunction`, `ExpDecayFunction`,
  `LinearDecayFunction` a enumy `ScoreMode`, `BoostMode`, `FieldValueFactorModifier`,
  `MultiValueMode`.
- Funkce pro `rank_feature`: `SaturationFunction`, `LogarithmFunction`, `SigmoidFunction`,
  `LinearFunction`.
- Ke každé nové query odpovídající resolver v `PhpQueryBuilder`.

#### Search — tělo requestu a výsledky

- **Highlight**: `Builder::setHighlight()`, třídy `Highlight` a `HighlightField` se 14 volbami
  (globálně i per pole), enumy `HighlighterType`, `HighlightOrder`, `BoundaryScanner`,
  `Encoder`, `Fragmenter`. `HitsCollection::getHighlights()` pro čtení výsledku.
- **Suggest**: `Builder::setSuggest()`, `TermSuggest`, `PhraseSuggest`, `CompletionSuggest`,
  `DirectGenerator`, kontejner `Suggest`, enumy `SuggestMode`, `SuggestSort`,
  `StringDistance`. `Result::getSuggests()` / `getSuggest()` s DTO `SuggestEntry`
  a `SuggestOption`.
- Další volby těla requestu na `Builder`: `setPostFilter()`, `minScore()`,
  `trackTotalHits()`, `trackScores()`, `addScriptField()`, `addRuntimeMapping()`
  a `addRescore()` s třídou `Rescore` a enumem `RescoreMode`.
- **Point in Time** pro konzistentní hluboké stránkování: `Connection::openPointInTime()`,
  `closePointInTime()`, třída `PointInTime` a `Builder::setPointInTime()`. S PIT se index
  neposílá v requestu (je součástí PIT), `Builder::build()` ho proto vynechá.

#### Indexing

- **Bulk API**: `Connection::bulk()` s `BulkRequest`, operacemi `IndexOperation`,
  `CreateOperation`, `UpdateOperation`, `DeleteOperation` a `BulkParams`.
  Odpověď `BulkResponse` má `hasErrors()`, `getErrors()` (DTO `BulkItemError`),
  `getSuccessCount()` a `getTook()` — bulk vrací HTTP 200 i při částečném selhání,
  takže kontrola chyb je na volajícím.

#### Search — agregace

- Metrické agregace: `AvgAggregation`, `ValueCountAggregation`, `StatsAggregation`,
  `ExtendedStatsAggregation`, `PercentilesAggregation`, `PercentileRanksAggregation`,
  `WeightedAvgAggregation` — každá s resolverem pro `PhpQueryBuilder`.
- `TermsAggregation` nově umí `shardSize()`, `minDocCount()`, `include()` a `exclude()`.
- Bucket agregace: `HistogramAggregation`, `DateHistogramAggregation`, `RangeAggregation`,
  `DateRangeAggregation`, `FiltersAggregation`, `MissingAggregation`, `MultiTermsAggregation`,
  `RareTermsAggregation`, `SignificantTermsAggregation`, `SamplerAggregation`
  a `CompositeAggregation` se zdroji `TermsSource`, `HistogramSource`, `DateHistogramSource`.
  Nový DTO `Range` pro `range` a `date_range`. Ke každé resolver pro `PhpQueryBuilder`.
- Pipeline agregace: `BucketSelectorAggregation`, `BucketScriptAggregation`,
  `BucketSortAggregation`, `DerivativeAggregation`, `CumulativeSumAggregation`
  a enum `GapPolicy`, opět včetně resolverů.

#### Mapping — nastavení indexu

- Atribut `Index` nese `number_of_shards`, `number_of_replicas`, `refresh_interval`,
  `max_ngram_diff` a `max_shingle_diff` (plus settery ke všem včetně `max_result_window`,
  aby se daly dolaďovat v post-event handleru).
- JSON driver čte nastavení indexu z `settings.index.*` i z plochého `settings.*`;
  dosud se zahazovalo úplně.

### Změněno

- ⚠️ **`TextType` posílá do mappingu všechny své parametry, ne jen `type` a `analyzer`.**
  Dosud se zahazovaly `search_analyzer`, `search_quote_analyzer`, `position_increment_gap`,
  `index_options`, `index_phrases`, `norms`, `similarity`, `term_vector`,
  `index_prefixes_min_chars` / `index_prefixes_max_chars` a `fielddata`.
  **Pokud je projekt nastavoval, změní se mu vygenerovaný mapping a index je potřeba
  vytvořit znovu a přeindexovat** — analyzery ani většinu těchto parametrů nelze
  u existujícího indexu měnit za provozu.
- ⚠️ **JSON driver čte konfiguraci properties.** `TextTypeFactory`, `KeywordTypeFactory`,
  `IntegerTypeFactory`, `FloatTypeFactory` a `BooleanTypeFactory` dosud nastavovaly jen
  jméno, takže z každé property zbylo `{"type": "text"}`. Nově se čtou všechny parametry
  včetně multi-fields (`fields`). Mění vygenerovaný mapping u projektů na JsonDriveru.
- ⚠️ **JSON driver respektuje `type` u analyzeru.** Dosud se ignoroval a vždy vznikl custom
  analyzer. Definice s `"type": "standard"` a zároveň tokenizerem se teď zpracuje jako
  vestavěný analyzer a tokenizer/filtry se zahodí.
- ⚠️ **Nové typy v `analysis` registrech.** Definice, které JSON driver dosud tiše zahazoval
  (`pattern_replace` filtr, tokenizery `keyword`/`whitespace`/`char_group`/…,
  `analysis.normalizer`), se teď do mappingu propíšou.
- `AnalyzerFactoryInterface::create()` vrací `AnalyzerInterface` místo `Analyzer`.
- `Analysis::addAnalyzer()` přijímá `AnalyzerInterface`; `Analysis` má nový nepovinný
  5. parametr konstruktoru `$normalizers`.
- `Analyzer` má nový nepovinný 4. parametr `$charFilters`.
- `AbstractParams::toArray()` serializuje backed enumy přes `->value`; větev s `toString()`
  zůstává pro objekty, které backed enum nejsou.
- **Opraveny překlepy v API; stará jména zůstávají jako `@deprecated` aliasy.**
  `AbstractCharactedFilter` → `AbstractCharacterFilter`, `MappingMetada` → `MappingMetadata`,
  `TokenizerResolver::resolvetTokenizer()` → `resolveTokenizer()`, namespace
  `Drivers\Factories\CharactedFilters` → `CharacterFilters`. Vlastní char filtry postavené
  na starém základu i volání starých jmen fungují dál.
  ⚠️ Jediná výjimka: `MappingMetadataFactory` a `MetadataProviderInterface` teď vracejí
  `MappingMetadata`. Kód, který si návratovou hodnotu type-hintuje starým jménem
  `MappingMetada`, přestane sedět — konstrukce `new MappingMetada(...)` a předání dál
  fungují beze změny.
- Nové query jsou v podsložkách podle kategorií z dokumentace Elasticsearche
  (`Queries\Compound`, `Queries\Specialized`). Stávající query zůstávají v `Queries`,
  jejich přesun by byl BC break a proběhne samostatně.

### Opraveno

- **Char filtry se zapisovaly pod `analysis.character_filter`**, Elasticsearch čte
  `analysis.char_filter` — index s char filtrem tedy nešlo vytvořit vůbec.
- **`PrefixQuery` s `rewrite` nebo `case_insensitive`** dávala tyto parametry vedle objektu
  pole místo dovnitř, takže ES vracel „`[prefix] query doesn't support multiple fields`".
- **`Language::DANISH`** měl hodnotu `danich`, kterou Elasticsearch odmítá — dánský stemmer
  nešlo použít. Opraveno na `danish`, což mění vygenerovaný mapping.
- **`PatternReplaceCharacterFilterFactory`** mapovala z devíti hodnot enumu `Flags` jen dvě,
  ostatní z JSONu tiše zmizely. Nově přes `Flags::tryFrom()`.
- **`PropertiesResolver`** u objektu vnořeného do objektu přidával property do rootu indexu
  místo do rodiče.
- **`max_result_window` se u indexu bez `analysis` nikdy neposlalo.**
  `MetadataRequestFactory::resolveSettings()` končil na `return null`, takže
  `#[Index(max_result_window: …)]` na entitě bez analyzerů nemělo žádný efekt.
- **`missing('0')` se u agregací tiše zahazovalo.** Podmínka `if ($this->missing)` je falsy
  check a `'0'` i `0` jsou v PHP falsy — opraveno na `null !== $this->missing` v deseti
  agregacích (`avg`, `min`, `max`, `sum`, `stats`, `extended_stats`, `percentiles`,
  `percentile_ranks`, `cardinality`, `terms`).
- **`GlobalAggregation` bez podagregací posílala `"aggs": []`**, což Elasticsearch odmítá
  (`Expected [START_OBJECT] under [aggs]`) — samostatně tedy nešla použít vůbec.
- **`ScaledFloatType` neposílal povinný `scaling_factor`**, takže `indices.create`
  s takovou property skončil na `Field [scaling_factor] is required`. Typ tedy nešlo
  použít vůbec.
- **`AliasType` měl v konstruktoru překlep `contect` místo `context`** — pojmenovaný
  argument `context:` padal na `Unknown named parameter`. Parametr je přejmenovaný;
  volání pozicí funguje beze změny, `contect:` už ne.
- `make phpstan` padal na `Allowed memory size exhausted` — target má `--memory-limit=512M`.

### Odebráno

- ⚠️ **`Connection\Params\SuggestMode`.** Nahrazen `Search\Suggest\Enums\SuggestMode`,
  který se používá jak v těle requestu, tak jako query parametr `suggest_mode`
  v `SearchParams`. Hodnoty jsou stejné, mění se jen namespace.
