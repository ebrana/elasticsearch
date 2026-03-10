<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Elasticsearch\Tools\PhpQueryBuilder;

$builder = new PhpQueryBuilder();
$result = $builder->fromJson('{
    "query" : {
         "term" : { "user.id" : "kimchy" }
    }
}');

var_dump($result);

