<?php

namespace Otomaties\ProductFilters\Filters;

class MetaFilter
{
    public function apply(array $args, array $filter, mixed $value): array
    {
        $metaKey = $filter['data']['meta_key'] ?? null;

        if (empty($metaKey)) {
            return $args;
        }

        $metaQuery = collect($args['meta_query'] ?? [])
            ->reject(fn ($query) => is_array($query)
                && ($query['key'] ?? null) === $metaKey);

        if (! empty($value)) {
            $metaQuery->push([
                'key' => $metaKey,
                'value' => $value,
            ]);
        }

        $args['meta_query'] = $metaQuery->toArray();

        return $args;
    }

    public static function options(string $metaKey, array $queriedObject, array $filteredProductQueryArgs): array
    {
        $selectedValues = collect($filteredProductQueryArgs['meta_query'] ?? [])
            ->filter(fn ($metaQueryItem) => is_array($metaQueryItem)
                && ($metaQueryItem['key'] ?? null) === $metaKey)
            ->flatMap(fn ($metaQueryItem) => is_array($metaQueryItem['value'] ?? null)
                ? $metaQueryItem['value']
                : [$metaQueryItem['value'] ?? null])
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $countQueryArgs = $filteredProductQueryArgs;

        if (! empty($countQueryArgs['meta_query']) && is_array($countQueryArgs['meta_query'])) {
            $filteredMetaQuery = array_filter(
                $countQueryArgs['meta_query'],
                fn ($metaQueryItem, $key) => $key === 'relation'
                    || ! is_array($metaQueryItem)
                    || ($metaQueryItem['key'] ?? null) !== $metaKey,
                ARRAY_FILTER_USE_BOTH
            );

            if (count($filteredMetaQuery) === 1 && isset($filteredMetaQuery['relation'])) {
                unset($countQueryArgs['meta_query']);
            } else {
                $countQueryArgs['meta_query'] = $filteredMetaQuery;
            }
        }

        $countQueryArgs['posts_per_page'] = -1;
        $countQueryArgs['paged'] = 1;
        $countQueryArgs['fields'] = 'ids';
        $countQueryArgs['no_found_rows'] = true;

        $productIds = get_posts($countQueryArgs);

        if (empty($productIds)) {
            if (empty($selectedValues)) {
                return [];
            }

            return collect($selectedValues)
                ->mapWithKeys(fn ($value) => [
                    (string) $value => [
                        'label' => $value,
                        'count' => 0,
                    ],
                ])
                ->toArray();
        }

        $metaCounts = [];
        foreach ($productIds as $productId) {
            $values = get_post_meta($productId, $metaKey, false);
            if (empty($values)) {
                continue;
            }

            foreach ($values as $value) {
                if ($value === '' || $value === null) {
                    continue;
                }

                $key = (string) $value;
                $metaCounts[$key] = ($metaCounts[$key] ?? 0) + 1;
            }
        }

        $options = collect($metaCounts)
            ->sortKeys()
            ->mapWithKeys(fn ($count, $value) => [
                $value => [
                    'label' => $value,
                    'count' => $count,
                ],
            ])
            ->toArray();

        if (! empty($selectedValues)) {
            foreach ($selectedValues as $selectedValue) {
                $selectedKey = (string) $selectedValue;
                if (! isset($options[$selectedKey])) {
                    $options[$selectedKey] = [
                        'label' => $selectedValue,
                        'count' => 0,
                    ];
                }
            }
        }

        return $options;
    }
}
