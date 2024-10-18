<?php

namespace Otomaties\ProductFilters\Filters;

use Illuminate\Support\Collection;
use Otomaties\ProductFilters\ProductFilters;

class TaxonomyFilter extends Filter
{
    private string $taxonomy;

    public function __construct(protected string $slug, array $params)
    {
        $this->taxonomy = $params['taxonomy'];

        parent::__construct($slug, $params);
    }

    public function options(): Collection
    {
        $queryArgs = array_merge(ProductFilters::baseQueryArgs(), [
            'posts_per_page' => -1,
            'fields' => 'ids',
        ]);

        $productIds = get_posts($queryArgs);
        $queriedObject = get_queried_object();

        return collect(wp_get_object_terms($productIds, $this->taxonomy(), [
            'hide_empty' => true,
            'parent' => ($queriedObject instanceof \WP_Term && $queriedObject->taxonomy === $this->taxonomy()) ? $queriedObject->term_id : 0,
        ]))->mapWithKeys(fn ($term) => [$term->slug => $term->name]);
    }

    public function taxonomy(): string
    {
        return $this->taxonomy;
    }

    public function modifyQueryArgs(array $args, mixed $value): array
    {
        $taxQuery = collect($args['tax_query'] ?? [])
            ->reject(fn ($query) => $query['taxonomy'] === $this->taxonomy());

        $value = array_filter((array) $value);
        if (! empty($value)) {
            $taxQuery->push([
                'taxonomy' => $this->taxonomy(),
                'field' => 'slug',
                'terms' => $value,
            ]);
        }

        $args['tax_query'] = $taxQuery->toArray();

        return $args;
    }
}
