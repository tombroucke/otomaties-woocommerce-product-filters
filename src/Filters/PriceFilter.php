<?php

namespace Otomaties\ProductFilters\Filters;

class PriceFilter
{
    public function apply(array $args, array $filter, array $value): array
    {
        $metaQuery = collect($args['meta_query'] ?? [])
            ->reject(fn ($query) => $query['key'] === '_price');

        extract($value);

        if ($min && $max) {
            $metaQuery->push([
                'key' => '_price',
                'value' => [$min, $max],
                'type' => 'numeric',
                'compare' => 'BETWEEN',
            ]);
        } elseif ($min) {
            $metaQuery->push([
                'key' => '_price',
                'value' => $min,
                'type' => 'numeric',
                'compare' => '>=',
            ]);
        } elseif ($max) {
            $metaQuery->push([
                'key' => '_price',
                'value' => $max,
                'type' => 'numeric',
                'compare' => '<=',
            ]);
        }

        $args['meta_query'] = $metaQuery->toArray();

        return $args;
    }
}
