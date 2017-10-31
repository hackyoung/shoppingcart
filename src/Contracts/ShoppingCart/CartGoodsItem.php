<?php
namespace Ylara\Contracts\ShoppingCart;

interface CartGoodsItem extends CartItem
{
    public function properties() : array;
}
