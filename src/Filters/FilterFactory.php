<?php

namespace Otomaties\ProductFilters\Filters;

use Illuminate\Support\Str;

class FilterFactory
{
    public static function create($component, $slug, $filter)
    {
        $file = Str::studly($component);

        if (isset($filter['type'])) {
            $file .= '\\'.Str::studly($filter['type']).Str::studly($component);
        }

        $class = 'Otomaties\ProductFilters\Filters\\'.$file;

        return new $class($slug, $filter);
    }
}
