<?php
namespace Ylara\ShoppingCart;

use Ylara\Contracts\ShoppingCart\CartStorage as CartStorageInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Cache\CacheManager;

class CartStorage implements CartStorageInterface
{
    protected $cart_id;

    protected $driver;

    protected $owner_ids;

    protected $owner_goods;

    public function __construct($cart_id)
    {
        $this->cart_id = $cart_id;
        $this->driver = app(CacheManager::class)->tags($cart_id);
        $this->owner_ids = $this->driver->get('owner_ids') ?? [];
        $this->owner_goods = $this->driver->many($this->ownerIds()) ?? [];
    }

    public function __destruct()
    {
        $this->driver->put('owner_ids', $this->owner_ids, 30);
        $this->driver->putMany($this->owner_goods, 30);
    }

    public function items() : array
    {
        $result = [];
        foreach ($this->ownerIds() as $owner_id) {
            $result += $this->itemsOf($owner_id);
        }

        return $result;
    }

    public function itemsOf($owner_id) : array
    {
        return $this->owner_goods[$owner_id] ?? [];
    }

    public function ownerIds() : array
    {
        return $this->owner_ids ?? [];
    }

    public function remove($item_id)
    {
        if ($this->locked()) {
            throw new \RuntimeException('购物车已锁定，无法移除条目');
        }
        foreach ($this->ownerIds() as $owner_id) {
            $this->itemsOf($owner_id);
            if (! isset($this->owner_goods[$owner_id][$item_id])) {
                continue;
            }
            $item = $this->owner_goods[$owner_id][$item_id];
            unset($this->owner_goods[$owner_id][$item_id]);
            if (empty($this->owner_goods[$owner_id])) {
                $key = array_search($owner_id, $this->owner_ids);
                if ($key > -1) {
                    array_splice($this->owner_ids, $key, 1);
                }
            }
            return $item;
        }
    }

    public function addItem(array $item, $owner_id, $type)
    {
        if ($this->locked()) {
            throw new \RuntimeException('购物车已锁定，无法添加条目');
        }
        $item['type'] = $type;
        if (! in_array($owner_id, $this->owner_ids)) {
            $this->owner_ids[] = $owner_id;
        }
        $this->owner_goods[$owner_id][$item['cart_item_id']] = $item;
    }

    public function item($item_id) : array
    {
        foreach ($this->ownerIds() as $owner_id) {
            $owner_goods = $this->itemsOf($owner_id);
            if (isset($owner_goods[$item_id])) {
                return $owner_goods[$item_id];
            }
        }

        return [];
    }

    public function clear()
    {
        if ($this->locked()) {
            throw new \RuntimeException('购物车已锁定，无法清空条目');
        }
        $this->owner_ids = [];
        $this->owner_goods = [];
    }

    public function isEmpty() : bool
    {
        if (empty($this->owner_ids)) {
            return true;
        }
        foreach ($this->ownerIds() as $owner_id) {
            if (! isset($this->owner_goods[$owner_id])) {
                continue;
            }
            if (! empty($this->owner_goods[$owner_id])) {
                return false;
            }
        }
        return true;
    }

    public function isEmptyOf($owner_id) : bool
    {
        if (! in_array($owner_id, $this->owner_ids)) {
            return true;
        }
        if (! $this->owner_goods[$owner_id]) {
            return true;
        }

        return empty($this->owner_goods[$owner_id]);
    }

    public function locked() : bool
    {
        return $this->driver->get('locked') ?? false;
    }

    public function unlock()
    {
        $this->driver->put('locked', false, 24*60);
    }

    public function lock()
    {
        $this->driver->put('locked', true, 24*60);
    }
}
