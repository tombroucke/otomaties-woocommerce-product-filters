<?php

namespace Otomaties\ProductFilters\Filters;

use Otomaties\ProductFilters\ProductFilters;

class MetaFilter extends Filter
{
    private string $metaKey;

    public function __construct(protected string $slug, array $params)
    {
        $this->metaKey = $params['meta_key'];

        parent::__construct($slug, $params);
    }

    public function options()
    {
        $queryArgs = array_merge(ProductFilters::baseQueryArgs(), [
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_key' => $this->metaKey(),
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'meta_query' => [
                [
                    'key' => $this->metaKey(),
                    'compare' => 'EXISTS',
                ],
            ],
        ]);

        return collect(get_posts($queryArgs))
            ->map(fn ($postId) => get_post_meta($postId, $this->metaKey(), true))
            ->mapWithKeys(fn ($value) => [$value => $value])
            ->unique()
            ->sort();
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
