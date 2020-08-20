<?php

namespace Caishni\Fawry\Tests;

use Caishni\Fawry\Contracts\FawryPayItem;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model implements FawryPayItem
{
    protected $fillable = ['amount'];
    protected $table = 'payments';
    public $timestamps = false;

    public function getItemIdAttribute()
    {
        return $this->id;
    }

    public function getItemPriceAttribute()
    {
        return $this->amount;
    }

    public function getItemDescriptionAttribute()
    {
        return 'test item';
    }
}