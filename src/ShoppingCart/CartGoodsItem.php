<?php
namespace Ylara\ShoppingCart;

use Ylara\Contracts\ShoppingCart\CartGoodsItem as CartGoodsItemInterface;

class CartGoodsItem extends CartItem implements CartGoodsItemInterface
{
    const ITEM_TYPE = 'goods';

    public function properties() : array
    {
        return $this->data['properties'] ?? [];
    }

    public function type()
    {
        return self::ITEM_TYPE;
    }
}
