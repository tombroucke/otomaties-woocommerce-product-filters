<?php

namespace Otomaties\ProductFilters\Facades;

use Illuminate\Support\Facades\Facade;

class ProductFilters extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ProductFilters';
    }
}
