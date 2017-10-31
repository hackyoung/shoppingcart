<?php
namespace Ylara\Contracts\ShoppingCart;

/**
 * 购物车接口
 */
interface ShoppingCart
{
    /**
     * 获取购物车中所有item
     *
     * @return Ylara\Constracts\ShoppingCart\CartItem[]
     */
    public function items() : array;

    /**
     * 获取某个用户添加到购物车的所有item
     *
     * @return Ylara\Constracts\ShoppingCart\CartItem[]
     */
    public function itemsOf($order_people) : array;

    /**
     * 获取所有购物车中添加商品的用户id
     *
     * @return string[]
     */
    public function orderPeoples() : array;

    /**
     * 向购物车中添加item
     */
    public function addItem(CartItem $item, $owner_id);

    /**
     * 通过item_id 查找item
     *
     * @return Ylara\Constracts\ShoppingCart\CartItem
     */
    public function item($item_id) : CartItem;

    /**
     * 通过item_id 从购物车移除item, 该方法不会检查是否是移除人和添加人的关系
     *
     * @return Ylara\Constracts\ShoppingCart\CartItem
     */
    public function remove($item_id) : CartItem;

    /**
     * 添加人自己移除item
     *
     * @return Ylara\Constracts\ShoppingCart\CartItem
     */
    public function removeByOwner($item_id, $owner_id) : CartItem;

    /**
     * 判断购物车是否空
     *
     * @return boolean
     */
    public function isEmpty() : bool;

    /**
     * 判断某人是否在购物车中添加过item
     *
     * @return boolean
     */
    public function isEmptyOf($order_people) : bool;

    /**
     * 清空购物车
     */
    public function clear();

    /**
     * 锁定购物车
     */
    public function lock();

    /**
     * 解锁购物车
     */
    public function unlock();

    /**
     * 判断购物车是否锁定
     */
    public function locked() : bool;
}
