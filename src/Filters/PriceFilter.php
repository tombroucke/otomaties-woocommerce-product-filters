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

    public static function limits(array $queriedObject, array $filteredProductQueryArgs): array
    {
        global $wpdb;

        $countQueryArgs = $filteredProductQueryArgs;
        $countQueryArgs['meta_query'] = collect($countQueryArgs['meta_query'] ?? [])
            ->reject(fn ($query) => isset($query['key']) && $query['key'] === '_price')
            ->values()
            ->toArray();

        // Remove the queried product_cat from tax_query
        if (($queriedObject['taxonomy'] ?? null) === 'product_cat' && ! empty($countQueryArgs['tax_query'])) {
            foreach ($countQueryArgs['tax_query'] as $key => $taxQueryItem) {
                if (($taxQueryItem['taxonomy'] ?? null) === 'product_cat'
                    && ($taxQueryItem['terms'] ?? null) === $queriedObject['terms']) {
                    unset($countQueryArgs['tax_query'][$key]);
                }
            }
        }

        $countQueryArgs['posts_per_page'] = -1;
        $countQueryArgs['paged'] = 1;
        $countQueryArgs['fields'] = 'ids';
        $countQueryArgs['no_found_rows'] = true;

        $productIds = get_posts($countQueryArgs);

        if (empty($productIds)) {
            return [
                'lower_limit' => 0,
                'upper_limit' => 0,
            ];
        }

        $placeholders = implode(', ', array_fill(0, count($productIds), '%d'));

        $query = $wpdb->prepare(
            "SELECT min(meta_value + 0) as min_price, max(meta_value + 0) as max_price FROM {$wpdb->postmeta} WHERE meta_key = '_price' AND post_id IN ($placeholders)",
            ...$productIds
        );

        $prices = $wpdb->get_row($query);

        return [
            'lower_limit' => $prices->min_price ?? 0,
            'upper_limit' => $prices->max_price ?? 0,
        ];
    }
}
