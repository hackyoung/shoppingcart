<?php
namespace Ylara\ShoppingCart;

use Ylara\Contracts\ShoppingCart\CartPackageItem as CartPackageItemInterface;
use Ylara\Contracts\ShoppingCart\CartGoodsItem as CartGoodsItemInterface;

class CartPackageItem extends CartItem implements CartPackageItemInterface
{
    const ITEM_TYPE = 'package';

    protected $items = [];

    public function items() : array
    {
        return $this->items;
    }

    public function addItem(CartGoodsItemInterface $item)
    {
        $this->items[] = $item;

        return $this;
    }

    public function type()
    {
        return self::ITEM_TYPE;
    }

    public function toArray() : array
    {
        $data = $this->data;
        $data['items'] = [];
        foreach ($this->items as $item) {
            $data['items'][] = $item->toArray();
        }

        return $data;
    }
}
