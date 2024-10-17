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
        $terms = get_terms([
            'taxonomy' => $this->taxonomy(),
            'hide_empty' => false,
        ]);

        return collect($terms)
            ->mapWithKeys(function ($term) {
                return [$term->slug => $term->name];
            })
            ->toArray();
    }

    public function taxonomy(): string
    {
        return $this->taxonomy;
    }

    public function modifyQueryArgs(array $args, mixed $value): array
    {
        $taxQuery = collect($args['tax_query'] ?? [])
            ->reject(function ($query) {
                return $query['taxonomy'] === $this->taxonomy();
            });

        if (empty($value)) {
            $args['tax_query'] = $taxQuery->toArray();

            return $args;
        }

        $args['tax_query'] = $taxQuery->push([
            'taxonomy' => $this->taxonomy(),
            'field' => 'slug',
            'terms' => $value,
        ])->toArray();

        return $args;
    }
}
