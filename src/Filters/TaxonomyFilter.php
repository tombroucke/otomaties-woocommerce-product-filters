<?php

namespace Otomaties\ProductFilters\Filters;

class TaxonomyFilter
{
    public function apply(array $args, array $filter, mixed $value): array
    {
        $taxQuery = collect($args['tax_query'] ?? []);

        if (! empty($value)) {
            $taxQuery->push([
                'taxonomy' => $filter['data']['taxonomy'],
                'field' => 'slug',
                'terms' => $value,
            ]);
        }

        $args['tax_query'] = $taxQuery->toArray();

        return $args;
    }

    public static function options(string $taxonomy, array $queriedObject, array $filteredProductQueryArgs): array
    {
        $queriedObjectTaxonomy = $queriedObject['taxonomy'] ?? null;
        $queriedObjectTermId = $queriedObject['terms'] ?? ($queriedObject['term_id'] ?? null);

        $selectedSlugs = collect($filteredProductQueryArgs['tax_query'] ?? [])
            ->filter(fn ($taxQueryItem) => ($taxQueryItem['taxonomy'] ?? null) === $taxonomy
                && ($taxQueryItem['field'] ?? null) === 'slug')
            ->flatMap(fn ($taxQueryItem) => is_array($taxQueryItem['terms'] ?? null)
                ? $taxQueryItem['terms']
                : [$taxQueryItem['terms'] ?? null])
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $countQueryArgs = $filteredProductQueryArgs;

        if (! empty($countQueryArgs['tax_query']) && is_array($countQueryArgs['tax_query'])) {
            $filteredTaxQuery = array_filter(
                $countQueryArgs['tax_query'],
                fn ($taxQueryItem, $key) => $key === 'relation'
                    || ($taxQueryItem['taxonomy'] ?? null) !== $taxonomy,
                ARRAY_FILTER_USE_BOTH
            );

            if (count($filteredTaxQuery) === 1 && isset($filteredTaxQuery['relation'])) {
                unset($countQueryArgs['tax_query']);
            } else {
                $countQueryArgs['tax_query'] = $filteredTaxQuery;
            }
        }
        $countQueryArgs['posts_per_page'] = -1;
        $countQueryArgs['paged'] = 1;
        $countQueryArgs['fields'] = 'ids';
        $countQueryArgs['no_found_rows'] = true;

        $productIds = get_posts($countQueryArgs);

        if (empty($productIds)) {
            if (empty($selectedSlugs)) {
                return [];
            }

            $selectedTerms = get_terms([
                'taxonomy' => $taxonomy,
                'slug' => $selectedSlugs,
                'hide_empty' => false,
            ]);

            if (is_wp_error($selectedTerms) || empty($selectedTerms)) {
                return [];
            }

            return collect($selectedTerms)
                ->mapWithKeys(fn ($term) => [
                    $term->slug => [
                        'label' => $term->name,
                        'count' => 0,
                    ],
                ])
                ->toArray();
        }

        $termsArgs = [
            'taxonomy' => $taxonomy,
            'hide_empty' => true,
            'object_ids' => $productIds,
        ];

        if (! empty($queriedObject) && $queriedObjectTaxonomy === $taxonomy && ! empty($queriedObjectTermId)) {
            $termsArgs['parent'] = (int) $queriedObjectTermId;
        } else {
            $termsArgs['parent'] = 0;
        }

        $termsWithObjectIds = wp_get_object_terms($productIds, $taxonomy, [
            'fields' => 'all_with_object_id',
        ]);

        $termCounts = [];
        foreach ($termsWithObjectIds as $term) {
            $termCounts[$term->term_id] = ($termCounts[$term->term_id] ?? 0) + 1;
        }

        $options = collect(get_terms($termsArgs))
            ->mapWithKeys(fn ($term) => [
                $term->slug => [
                    'label' => $term->name,
                    'count' => $termCounts[$term->term_id] ?? 0,
                ],
            ])
            ->toArray();

        if (! empty($selectedSlugs)) {
            $selectedTerms = get_terms([
                'taxonomy' => $taxonomy,
                'slug' => $selectedSlugs,
                'hide_empty' => false,
            ]);

            if (! is_wp_error($selectedTerms)) {
                foreach ($selectedTerms as $selectedTerm) {
                    if (! isset($options[$selectedTerm->slug])) {
                        $options[$selectedTerm->slug] = [
                            'label' => $selectedTerm->name,
                            'count' => $termCounts[$selectedTerm->term_id] ?? 0,
                        ];
                    }
                }
            }
        }

        return $options;
    }
}
