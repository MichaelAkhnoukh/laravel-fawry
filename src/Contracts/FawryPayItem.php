<?php

namespace Caishni\Fawry\Contracts;

interface FawryPayItem
{
    public function getItemIdAttribute();
    public function getItemPriceAttribute();
    public function getItemDescriptionAttribute();
}