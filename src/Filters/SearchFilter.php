<?php

namespace Otomaties\ProductFilters\Filters;

class SearchFilter
{
    public function apply(array $args, array $filter, string $value): array
    {
        if (empty($value)) {
            unset($args['s']);
        } else {
            $args['s'] = $value;
        }

        return $args;
    }
}
