<?php

namespace Otomaties\ProductFilters;

class ProductFilters
{
    public static function baseQueryArgs()
    {
        $baseQueryArgs = [
            'post_status' => 'publish',
            'post_type' => 'product',
        ];
        $queriedObject = get_queried_object();

        if ($queriedObject instanceof \WP_Term) {
            $baseQueryArgs['tax_query'] = [
                [
                    'taxonomy' => $queriedObject->taxonomy,
                    'field' => 'term_id',
                    'terms' => $queriedObject->term_id,
                ],
            ];
        }

        return $baseQueryArgs;
    }
}
