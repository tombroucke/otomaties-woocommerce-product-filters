<?php

namespace Otomaties\ProductFilters\Filters;

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
        $args = [
            'post_type' => 'product',
            'posts_per_page' => -1,
            'meta_key' => $this->metaKey(),
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'fields' => 'ids',
            'meta_query' => [
                [
                    'key' => $this->metaKey(),
                    'compare' => 'EXISTS',
                ],
            ],
        ];

        return collect(get_posts($args))
            ->map(fn ($postId) => get_post_meta($postId, $this->metaKey(), true))
            ->mapWithKeys(fn ($value) => [$value => $value])
            ->unique()
            ->sort();
    }

    public function metaKey(): string
    {
        return $this->metaKey;
    }

    public function modifyQueryArgs(array $args, array $values): array
    {
        $value = $values[0] ?? null;

        $metaQuery = collect($args['meta_query'] ?? [])
            ->reject(function ($query) {
                return $query['key'] === $this->metaKey();
            });

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
