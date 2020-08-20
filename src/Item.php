<?php

namespace Caishni\Fawry;

use Illuminate\Contracts\Support\Arrayable;

class Item implements Arrayable
{
    public $id;
    public $description;
    public $price;
    public $quantity;

    public function __construct($item, $key)
    {
        $this->id = $item->item_id;
        $this->description = $item->item_description;
        $this->price = number_format($item->item_price, 2, '.', '');
        $this->quantity = $item->quantity ?? 1;
    }

    public function toArray()
    {
        return [
            'itemId' => (string)$this->id,
            'description' => $this->description,
            'price' => (float)$this->price,
            'quantity' => $this->quantity
        ];
    }
}