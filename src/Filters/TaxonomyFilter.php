<?php

namespace Otomaties\ProductFilters\Filters;

use Illuminate\Support\Facades\Cache;
use Otomaties\ProductFilters\ProductFilters;

class TaxonomyFilter extends Filter
{
    private string $taxonomy;

    public function __construct(protected string $slug, array $params)
    {
        $this->taxonomy = $params['taxonomy'];

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

        $cacheKey = 'product_taxonomy_filter_ids_'.md5(serialize($queryArgs));

        return Cache::rememberForever($cacheKey, fn () => get_posts($queryArgs));
    }

    public function options(?string $queriedObjectTaxonomy, ?int $queriedObjectTermId, array $filterValues): array
    {

        // Remove price filters for taxonomy counts
        $filterValuesWithoutPrice = $filterValues;
        unset($filterValuesWithoutPrice['price_min'], $filterValuesWithoutPrice['price_max']);
        $productIds = $this->productsForCurrentFilters($queriedObjectTaxonomy, $queriedObjectTermId, $filterValuesWithoutPrice);

        // Get all terms for this taxonomy that are on the filtered products or their ancestors
        $allTerms = wp_get_object_terms($productIds, $this->taxonomy(), [
            'hide_empty' => true,
        ]);

        // Get parent term IDs from the directly assigned terms to include ancestor categories
        $parentTermIds = [];
        foreach ($allTerms as $term) {
            $ancestors = get_ancestors($term->term_id, $this->taxonomy(), 'taxonomy');
            $parentTermIds = array_merge($parentTermIds, $ancestors);
        }

        // Fetch parent terms
        $parentTerms = [];
        if (! empty($parentTermIds)) {
            $parentTerms = get_terms([
                'taxonomy' => $this->taxonomy(),
                'include' => array_unique($parentTermIds),
                'hide_empty' => false,
            ]);
        }

        // Merge direct terms and parent terms
        $terms = array_merge($allTerms, is_array($parentTerms) ? $parentTerms : []);

        // Filter to show only relevant hierarchy level
        if ($queriedObjectTaxonomy === $this->taxonomy()) {
            // On a taxonomy archive, show children of the current term
            $terms = array_filter($terms, fn ($term) => $term->parent == $queriedObjectTermId);
        } else {
            // Not on a taxonomy archive, show top-level terms
            $terms = array_filter($terms, fn ($term) => $term->parent == 0);
        }

        // Count products for each term based on the filtered product set
        return collect($terms)->mapWithKeys(function ($term) use ($productIds) {
            // Get all child term IDs recursively to include products from subcategories
            $termIds = [$term->term_id];
            $childTermIds = get_term_children($term->term_id, $this->taxonomy());
            if (! is_wp_error($childTermIds)) {
                $termIds = array_merge($termIds, $childTermIds);
            }

            // Count how many of the filtered products have this term or any child term
            $termProductIds = get_objects_in_term($termIds, $this->taxonomy());
            $count = count(array_intersect($productIds, $termProductIds));

            return [$term->slug => [
                'label' => $term->name,
                'count' => $count,
            ]];
        })
            ->filter(fn ($option) => $option['count'] > 0) // Only show terms with products
            ->toArray();
    }

    public function taxonomy(): string
    {
        return $this->taxonomy;
    }

    public function modifyQueryArgs(array $args, mixed $value): array
    {
        // Find the queried object tax query (added by productsForCurrentFilters) to preserve it
        $queriedObjectQuery = null;
        $taxQuery = collect($args['tax_query'] ?? [])
            ->reject(function ($query) use (&$queriedObjectQuery) {
                if ($query['taxonomy'] === $this->taxonomy()) {
                    // If this uses term_id field, it's the queried object query - preserve it
                    if (isset($query['field']) && $query['field'] === 'term_id') {
                        $queriedObjectQuery = $query;
                    }

                    return true; // Remove all queries for this taxonomy
                }

                return false;
            });

        $value = array_filter((array) $value);
        if (! empty($value)) {
            $taxQuery->push([
                'taxonomy' => $this->taxonomy(),
                'field' => 'slug',
                'terms' => $value,
            ]);
        }

        // Re-add the queried object query if it existed
        if ($queriedObjectQuery) {
            $taxQuery->push($queriedObjectQuery);
        }

        $args['tax_query'] = $taxQuery->toArray();

        return $args;
    }
}
