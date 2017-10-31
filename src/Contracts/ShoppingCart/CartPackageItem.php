<?php
namespace Ylara\Contracts\ShoppingCart;

interface CartPackageItem extends CartItem
{
    /**
     * 套餐包含多个单品，这里返回CartGoodsItem[]
     */
    public function items() : array;

    public function addItem(CartGoodsItem $item);
}
