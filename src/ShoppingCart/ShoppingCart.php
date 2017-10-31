<?php
namespace Ylara\ShoppingCart;

use Ylara\Contracts\ShoppingCart\ShoppingCart as ShoppingCartInterface;
use Ylara\Contracts\ShoppingCart\CartGoodsItem as CartGoodsItemInterface;
use Ylara\Contracts\ShoppingCart\CartItem as CartItemInterface;
use Ylara\Contracts\ShoppingCart\CartPackageItem as CartPackageItemInterface;
use Ylara\Contracts\ShoppingCart\CartStorage;

class ShoppingCart implements ShoppingCartInterface
{
    protected $cart_id;
    protected $store;

    public function __construct($cart_id)
    {
        $this->cart_id = $cart_id;
        $this->store = app(CartStorage::class, [$this->cart_id]);
    }

    public function items() : array
    {
        $items_data = $this->store->items();
        $result = [];
        foreach ($items_data as $item_data) {
            $result[] = $this->packItem($item_data);
        }

        return $result;
    }

    public function item($item_id)
    {
        $item_data = $this->store->item($item_id);
        return $this->packItem($item_data);
    }

    public function itemsOf($order_people_id) : array
    {
        $result = [];
        foreach ($this->store->itemsOf($order_people_id) as $item_data) {
            $result[] = $this->packItem($item_data);
        }

        return $result;
    }

    public function orderPeoples() : array
    {
        return $this->store->ownerIds();
    }

    public function addItem(CartItemInterface $item, $order_people_id)
    {
        $item_data = $item->toArray();

        return $this->store->addItem($item_data, $order_people_id, $item->type());
    }

    public function remove($item_id)
    {
        $item_data = $this->store->remove($item_id);
        if ($item_data) {
            return $this->packItem($item_data);
        }
    }

    public function removeByOwner($item_id, $owner_id) : bool
    {
        $item_data = $this->store->item($item_id);
        $item = $this->packItem($item_data);
        if ($item->ownerId() != $owner_id) {
            return false;
        }

        return $this->remove($item_id);
    }

    public function isEmpty() : bool
    {
        return $this->store->isEmpty();
    }

    public function isEmptyOf($order_people_id) : bool
    {
        return $this->store->isEmptyOf($order_people_id);
    }

    public function clear()
    {
        $this->store->clear();
    }

    public function lock()
    {
        $this->store->lock();
    }

    public function unlock()
    {
        $this->store->unlock();
    }

    public function locked() : bool
    {
        return $this->store->locked();
    }

    protected function packItem($item_data)
    {
        switch ($item_data['type']) {
            case 'goods':
                return $this->packGoodsItem($item_data);
            case 'package':
                return $this->packPackageItem($item_data);
            default:
                throw new \RuntimeException('不支持的购物车元素类型');
        }
    }

    protected function packPackageItem($item_data)
    {
        $package_item = app(CartPackageItemInterface::class);
        $package_item->fill($item_data);
        foreach ($item_data['items'] ?? [] as $item) {
            $goods_item = app(CartGoodsItemInterface::class);
            $goods_item->fill($item);
            $package_item->addItem($goods_item);
        }

        return $package_item;
    }

    protected function packGoodsItem($item_data)
    {
        $goods_item = app(CartGoodsItemInterface::class);
        $goods_item->fill($item_data);

        return $goods_item;
    }
}
