<?php

namespace Otomaties\ProductFilters\Filters;

class Price extends Filter
{
    public function modifyQueryArgs(array $args, array $values): array
    {
        $min = $values[0] ?? null;
        $max = $values[1] ?? null;

        $metaQuery = collect($args['meta_query'] ?? [])
            ->reject(fn ($query) => $query['key'] === '_price');

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
