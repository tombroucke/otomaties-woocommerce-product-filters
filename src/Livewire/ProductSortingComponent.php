<?php

namespace Otomaties\ProductFilters\Livewire;

use Livewire\Attributes\Url;
use Livewire\Component;

class ProductSortingComponent extends Component
{
    public $options = [];

    #[Url('orderby')]
    public $selected = '';

    public function mount()
    {
        $this->options = apply_filters(
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

        $defaultOrderby = wc_get_loop_prop('is_search') ? 'relevance' : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby', ''));
        $this->selected = isset($_GET['orderby']) ? wc_clean(wp_unslash($_GET['orderby'])) : $defaultOrderby;
    }

    public function updated()
    {
        $this->dispatch('sortby-updated', $this->selected);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('ProductFilters::livewire.product-sorting');
    }
}
