<?php
namespace Ylara\Providers;

use Illuminate\Support\ServiceProvider;
use Ylara\ShoppingCart\Base\ShoppingCart as ShoppingCartImp;
use Ylara\ShoppingCart\Base\CartGoodsItem as CartGoodsItemImp;
use Ylara\ShoppingCart\Base\CartPackageItem as CartPackageItemImp;
use Ylara\ShoppingCart\Base\CartStorage as CartStorageImp;

use Ylara\Contracts\ShoppingCart\ShoppingCart;
use Ylara\Contracts\ShoppingCart\CartGoodsItem;
use Ylara\Contracts\ShoppingCart\CartPackageItem;
use Ylara\Contracts\ShoppingCart\CartStorage;

class ShoppingCartServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ShoppingCart::class, function ($app, $params) {
            $rc = new \ReflectionClass(ShoppingCartImp::class);
            return $rc->newInstanceArgs($params);
        });

        $this->app->bind(CartGoodsItem::class, function () {
            return app(CartGoodsItemImp::class);
        });

        $this->app->bind(CartPackageItem::class, function () {
            return app(CartPackageItemImp::class);
        });

        $this->app->bind(CartStorage::class, function ($app, $params) {
            $rc = new \ReflectionClass(CartStorageImp::class);
            return $rc->newInstanceArgs($params);
        });
    }
}
