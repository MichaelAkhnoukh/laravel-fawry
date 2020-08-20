<?php

namespace Caishni\Fawry\Traits;

use Caishni\Fawry\Models\UserCard;

trait HasCards
{
    public function cards()
    {
        return $this->hasMany(UserCard::class);
    }
}