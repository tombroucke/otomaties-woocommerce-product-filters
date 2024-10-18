<?php

namespace Otomaties\ProductFilters\Filters;

class TaxonomyFilter extends Filter
{
    private string $taxonomy;

    public function __construct(protected string $slug, array $params)
    {
        $this->taxonomy = $params['taxonomy'];

        parent::__construct($slug, $params);
    }

    public function options()
    {
        $queriedObject = get_queried_object();

        $args = [
            'taxonomy' => $this->taxonomy(),
            'hide_empty' => false,
            'parent' => ($queriedObject instanceof \WP_Term && $queriedObject->taxonomy === $this->taxonomy()) ? $queriedObject->term_id : 0,
        ];

        return collect(get_terms($args))
            ->mapWithKeys(function ($term) {
                return [$term->slug => $term->name];
            });
    }

    public function taxonomy(): string
    {
        return $this->taxonomy;
    }

    public function modifyQueryArgs(array $args, array $values): array
    {
        $value = $values[0] ?? null;

        $taxQuery = collect($args['tax_query'] ?? [])
            ->reject(fn ($query) => $query['taxonomy'] === $this->taxonomy());

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
