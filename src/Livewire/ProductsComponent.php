<?php

namespace Otomaties\ProductFilters\Livewire;

use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Otomaties\ProductFilters\Filters\MetaFilter;
use Otomaties\ProductFilters\Filters\PriceFilter;
use Otomaties\ProductFilters\Filters\TaxonomyFilter;

class ProductsComponent extends Component
{
    public int $postsPerPage;

    #[Url]
    public int $page = 1;

    public array $queriedObject = [];

    public array $orderByOptions = [];

    #[Url('orderby')]
    public string $orderBy = '';

    public array $filters = [];

    public array $activeFilters = [];

    public function mount()
    {
        $this->postsPerPage = apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());
        $this->queriedObject = $this->queriedObjectToArray();
        $this->orderByOptions = $this->orderbyOptions();
        $this->orderBy = $this->orderBy();
        $this->filters = $this->filters();

        foreach ($this->filters as $key => $filter) {
            $this->activeFilters[$key] = $this->getValue($key);
        }
    }

    #[On('filter-updated')]
    public function updateFilter($data)
    {
        $this->activeFilters[$data['key']] = $data['value'];
        $this->page = 1;

        $this->dispatch('filters-updated');
    }

    private function getValue($key)
    {
        $filter = $this->filters[$key];
        $query = request()->query();

        if ($filter['component'] === 'price') {
            return [
                'min' => $query['price_min'] ?? null,
                'max' => $query['price_max'] ?? null,
            ];
        }

        if (isset($query[$key])) {
            return $query[$key];
        }

        return $this->getDefaultValue($filter);
    }

    private function getDefaultValue($filter)
    {
        return match ($filter['component']) {
            'checkbox' => [],
            'select' => '',
            'price' => [
                'min' => 0,
                'max' => 10000,
            ],
            default => null
        };
    }

    private function queriedObjectToArray(): array
    {
        $queriedObject = get_queried_object();
        if (! $queriedObject instanceof \WP_Term) {
            return [];
        }

        return [
            'taxonomy' => $queriedObject->taxonomy,
            'field' => 'term_id',
            'terms' => $queriedObject->term_id,
        ];
    }

    private function orderbyOptions(): array
    {
        return apply_filters(
            'woocommerce_catalog_orderby',
            [
                'menu_order' => __('Default sorting', 'woocommerce'),
                'popularity' => __('Sort by popularity', 'woocommerce'),
                'rating' => __('Sort by average rating', 'woocommerce'),
                'date' => __('Sort by latest', 'woocommerce'),
                'price' => __('Sort by price: low to high', 'woocommerce'),
                'price-desc' => __('Sort by price: high to low', 'woocommerce'),
            ]
        );
    }

    private function orderBy()
    {
        return isset($_GET['orderby'])
            ? wc_clean(wp_unslash($_GET['orderby']))
            : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby', ''));
    }

    public function filters()
    {
        return collect(config('product-filters.filters'))
            ->map(function ($filter, $key) {
                return [
                    'title' => $filter['title'],
                    'component' => $filter['component'],
                    'data' => array_filter([
                        'type' => $filter['type'] ?? null,
                        'taxonomy' => $filter['taxonomy'] ?? null,
                        'meta_key' => $filter['meta_key'] ?? null,
                    ]),
                ];
            })
            ->toArray();
    }

    #[Computed]
    private function filteredProductQueryArgs()
    {
        $args = array_merge(
            $this->baseQueryArgs(),
            ['paged' => $this->page]
        );

        return $this->applyFilters($args);
    }

    private function filteredProducts()
    {
        $args = $this->appendOrderingArgs($this->filteredProductQueryArgs());

        return (new Collection(get_posts($args)))
            ->map(fn ($productId) => wc_get_product($productId));
    }

    private function applyFilters($args)
    {
        foreach ($this->filters as $key => $filter) {
            if (empty($this->activeFilters[$key])) {
                continue;
            }

            $args = match ($filter['component']) {
                'price' => (new PriceFilter)->apply($args, $filter, $this->activeFilters[$key]),
                'checkbox' => ($filter['data']['type'] ?? null) === 'meta'
                    ? (new MetaFilter)->apply($args, $filter, $this->activeFilters[$key])
                    : (new TaxonomyFilter)->apply($args, $filter, $this->activeFilters[$key]),
                'select' => ($filter['data']['type'] ?? null) === 'meta'
                    ? (new MetaFilter)->apply($args, $filter, $this->activeFilters[$key])
                    : (new TaxonomyFilter)->apply($args, $filter, $this->activeFilters[$key]),
                default => $args,
            };
        }

        return $args;
    }

    private function appendOrderingArgs(array $args): array
    {
        switch ($this->orderBy) {
            case 'price':
                $args['meta_key'] = '_price';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'ASC';
                break;
            case 'price-desc':
                $args['meta_key'] = '_price';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'date':
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
            case 'popularity':
                $args['meta_key'] = 'total_sales';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'rating':
                $args['meta_key'] = '_wc_average_rating';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            default:
                break;
        }

        return $args;
    }

    #[Computed]
    public function foundProducts(): int
    {
        $args = array_merge(
            $this->baseQueryArgs(),
            ['posts_per_page' => -1]
        );

        $argWithFilters = $this->applyFilters($args);

        $query = new \WP_Query($argWithFilters);

        return (int) $query->found_posts;
    }

    #[Computed]
    public function pages(): array
    {
        $query = new \WP_Query($this->filteredProductQueryArgs());

        $total = (int) $query->max_num_pages;
        $current = (int) max(1, $this->page);

        $start = max(1, $current - 3);
        $end = min($total, $current + 3);

        if ($end - $start < 6) {
            if ($start === 1) {
                $end = min($total, $start + 5);
            } elseif ($end === $total) {
                $start = max(1, $end - 5);
            }
        }

        $pages = [];

        for ($i = $start; $i <= $end; $i++) {
            $pages[] = [
                'active' => $i === $current,
                'page' => $i,
            ];
        }

        return $pages;
    }

    private function baseQueryArgs(): array
    {
        $baseQueryArgs = [
            'post_status' => 'publish',
            'post_type' => 'product',
            'fields' => 'ids',
            'posts_per_page' => $this->postsPerPage,
        ];

        if (! empty($this->queriedObject)) {
            $baseQueryArgs['tax_query'] = [
                [
                    'taxonomy' => $this->queriedObject['taxonomy'],
                    'field' => 'term_id',
                    'terms' => $this->queriedObject['terms'],
                ],
            ];
        }

        return $baseQueryArgs;
    }

    public function goToPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('product-filters::livewire.products', [
            'products' => $this->filteredProducts(),
        ]);
    }
}
