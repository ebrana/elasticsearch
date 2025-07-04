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

[]() > [Ukázka použítí](../../../examples/searchData.php) <

[<< zpět](../../../README.md)
