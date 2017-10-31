<?php
namespace Ylara\ShoppingCart;

use Ylara\Contracts\ShoppingCart\CartItem as CartItemInterface;

abstract class CartItem implements CartItemInterface
{
    protected $data;

    public function __construct($item_id = null)
    {
        $this->data['cart_item_id'] = $item_id ?? uuid();
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __get($key)
    {
        return $this->data[$key] ?? null;
    }

    public function __call($method_name, $args = null)
    {
        $key = snake_case($method_name);
        if ($args != null && isset($this->data[$key])) {
            return $this->data[$key] = $args;
        }
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
    }

    public function id()
    {
        return $this->data['cart_item_id'];
    }

    public function name()
    {
        return $this->data['name'];
    }

    public function fill(array $data)
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    public function losts() : array
    {
        $keys = [
            'order_people_id', 'name', 'quantity',
            'price', 'order_time', 'stock_amount', 'unit',
            'content_id', 'menu_content_id'
        ];

        return array_intersect(array_diff($keys, array_keys($this->data)), $keys);
    }

    public function toArray() : array
    {
        return $this->data;
    }

    abstract public function type();
}
