<?php

namespace Otomaties\ProductFilters\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Otomaties\ProductFilters\ProductFilters;

class ProductsComponent extends Component
{
    public $postsPerPage;

    public $taxonomy = null;

    public $termId = null;

    public $orderBy = 'menu_order';

    #[Url]
    public $page = 1;

    public $queryArgs = [];

    public function mount()
    {
        $this->postsPerPage = apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());
        $this->queryArgs = ProductFilters::baseQueryArgs();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $this->buildQueryArgs();

        return view('product-filters::livewire.products', [
            'productQuery' => new \WP_Query($this->buildQueryArgs()),
        ]);
    }

    public function buildQueryArgs()
    {
        $args = $this->queryArgs;
        $args['posts_per_page'] = $this->postsPerPage;
        $args['paged'] = $this->page;

        $orderingArgs = WC()->query->get_catalog_ordering_args($this->orderBy);

        $queryParams = request()->query();
        $filters = app('product-filters::filters');

        foreach ($queryParams as $key => $value) {
            $filter = $filters->get($key);
            if ($filter) {
                $args = $filter->modifyQueryArgs($args, $value);
            }
        }

        $priceMin = $queryParams['price_min'] ?? null;
        $priceMax = $queryParams['price_max'] ?? null;

        if ($priceMin || $priceMax) {
            $args = $filters->get('price')->modifyQueryArgs($args, ['min' => $priceMin, 'max' => $priceMax]);
        }

        return array_merge($args, $orderingArgs);
    }

    #[On('filter-updated')]
    public function applyFilters($slug, $value)
    {
        $this->queryArgs = app('product-filters::filters')
            ->get($slug)
            ->modifyQueryArgs($this->queryArgs, $value);
    }

    #[On('sortby-updated')]
    public function sort($sortKey)
    {
        $this->orderBy = $sortKey;
    }

    #[Computed]
    public function pages()
    {
        $query = new \WP_Query($this->buildQueryArgs());

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

    public function goToPage($page)
    {
        $this->page = $page;
    }
}
