<?php

namespace Caishni\Fawry\Facades;

use Illuminate\Support\Facades\Facade;

class FawryPay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'fawrypay';
    }
}