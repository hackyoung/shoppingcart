<?php
namespace Ylara\Contracts\ShoppingCart;

interface CartItem
{
    public function id();

    public function fill(array $data);

    public function losts() : array;

    public function toArray() : array;

    public function type();
}
