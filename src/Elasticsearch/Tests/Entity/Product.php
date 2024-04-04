<?php declare(strict_types=1);

namespace Elasticsearch\Tests\Entity;

use Elasticsearch\Indexing\Interfaces\IndexableEntityInterface;
use Elasticsearch\Tests\Entity\Abstracted\AbstractProduct;

final class Product extends AbstractProduct implements IndexableEntityInterface
{
    public static function create(): self
    {
        $product = new Product();
        $product->setPk('test');
        $product->setProductTags('tv');
        $product->setParameters(10);
        $product->setParameterValues(100);

        $childEntityCs = new Translations('cs', 5000.0, 'xxx cs', 'test cs', 'another cs');
        $childEntityCs->setSellingPrice('1000');
        $childEntityCs->setSellingPriceWithVat('2000');
        $childEntityEn = new Translations('en', 1000.0, 'yyy en', 'test en', 'another en');
        $childEntityEn->setSellingPrice('1100');
        $childEntityEn->setSellingPriceWithVat('2100');
        $childEntitySk = new Translations('sk', 6000.0, 'ooo sk', 'test sk', 'another sk');
        $childEntitySk->setSellingPrice('3000');
        $childEntitySk->setSellingPriceWithVat('4000');

        $product->addSellingPrice($childEntityCs);
        $product->addSellingPrice($childEntityEn);
        $product->addSellingPrice($childEntitySk);


        $product->addSellingPriceWithVat($childEntityCs);
        $product->addSellingPriceWithVat($childEntityEn);
        $product->addSellingPriceWithVat($childEntitySk);

        $product->addTranslations($childEntityCs);
        $product->addTranslations($childEntityEn);
        $product->addTranslations($childEntitySk);

        return $product;
    }
}
