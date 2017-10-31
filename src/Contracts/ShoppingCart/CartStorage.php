<?php
namespace Ylara\Contracts\ShoppingCart;

interface CartStorage
{
    public function items() : array;

    public function addItem(array $item, $owner_id, $type);

    public function ownerIds() : array;

    public function remove($item_id);

    public function item($item_id) : array;

    public function clear();

    public function isEmpty() : bool;

    public function isEmptyOf($owner_id) : bool;

    public function lock();

    public function unlock();

    public function locked();
}
