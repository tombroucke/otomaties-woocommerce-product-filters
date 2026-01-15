<?php

namespace Otomaties\ProductFilters\Filters;

use Illuminate\Support\Facades\Cache;
use Otomaties\ProductFilters\ProductFilters;

class MetaFilter extends Filter
{
    private string $metaKey;

    public function __construct(protected string $slug, array $params)
    {
        $this->metaKey = $params['meta_key'];

        parent::__construct($slug, $params);
    }

    private function productsForCurrentFilters(?string $queriedObjectTaxonomy, ?int $queriedObjectTermId, array $filterValues)
    {
        $filters = app('product-filters::filters');

        // Start with base query args
        $queryArgs = array_merge(ProductFilters::baseQueryArgs(), [
            'posts_per_page' => -1,
            'fields' => 'ids',
        ]);
        
        // Include the queried object (e.g., current product category) in the query
        if ($queriedObjectTaxonomy && $queriedObjectTermId) {
            $queryArgs['tax_query'] = $queryArgs['tax_query'] ?? [];
            $queryArgs['tax_query'][] = [
                'taxonomy' => $queriedObjectTaxonomy,
                'field' => 'term_id',
                'terms' => [$queriedObjectTermId],
            ];
        }
        
        // Apply all filters except the current one
        foreach ($filterValues as $key => $value) {
            if ($key === $this->slug) {
                continue; // Skip current filter
            }
            
            // Skip price_min and price_max as they're handled separately below
            if (in_array($key, ['price_min', 'price_max'])) {
                continue;
            }
            
            $filter = $filters->get($key);
            if ($filter) {
                $queryArgs = $filter->modifyQueryArgs($queryArgs, $value);
            }
        }
        
        // Handle price filter separately if it exists
        $priceMin = $filterValues['price_min'] ?? null;
        $priceMax = $filterValues['price_max'] ?? null;
        
        if (($priceMin || $priceMax) && $this->slug !== 'price') {
            $queryArgs = $filters->get('price')->modifyQueryArgs($queryArgs, ['min' => $priceMin, 'max' => $priceMax]);
        }

        $cacheKey = 'product_meta_filter_ids_' . md5(serialize($queryArgs));

        return Cache::rememberForever($cacheKey, fn() => get_posts($queryArgs));
    }

    public function options(?string $queriedObjectTaxonomy = null, ?int $queriedObjectTermId = null, array $filterValues = [])
    {
        $productIds = $this->productsForCurrentFilters($queriedObjectTaxonomy, $queriedObjectTermId, $filterValues);

        // If no products match filters, return empty array
        if (empty($productIds)) {
            return [];
        }

        // Get all unique meta values from the filtered products
        $metaValues = collect($productIds)
            ->map(fn ($postId) => get_post_meta($postId, $this->metaKey(), true))
            ->filter() // Remove empty values
            ->unique()
            ->sort();

        // Count products for each meta value
        return $metaValues->mapWithKeys(function ($value) use ($productIds) {
            // Count how many of the filtered products have this meta value
            $count = 0;
            foreach ($productIds as $postId) {
                if (get_post_meta($postId, $this->metaKey(), true) === $value) {
                    $count++;
                }
            }
            
            return [$value => [
                'label' => $value,
                'count' => $count,
            ]];
        })
        ->filter(fn ($option) => $option['count'] > 0) // Only show values with products
        ->toArray();
    }

    public function metaKey(): string
    {
        return $this->metaKey;
    }

    public function modifyQueryArgs(array $args, mixed $value): array
    {
        $metaQuery = collect($args['meta_query'] ?? [])
            ->reject(fn ($query) => $query['key'] === $this->metaKey());

        if (! empty($value)) {
            $metaQuery->push([
                'key' => $this->metaKey(),
                'value' => $value,
            ]);
        }

        $args['meta_query'] = $metaQuery->toArray();

        return $args;
    }
}
