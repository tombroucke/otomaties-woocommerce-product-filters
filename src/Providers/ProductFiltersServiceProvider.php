<?php

namespace Otomaties\ProductFilters\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Otomaties\ProductFilters\Filters\FilterFactory;
use Otomaties\ProductFilters\Livewire\Filters\CheckboxComponent;
use Otomaties\ProductFilters\Livewire\Filters\PriceComponent;
use Otomaties\ProductFilters\Livewire\Filters\RadioComponent;
use Otomaties\ProductFilters\Livewire\Filters\SelectComponent;
use Otomaties\ProductFilters\Livewire\ProductFiltersComponent;
use Otomaties\ProductFilters\Livewire\ProductsComponent;
use Otomaties\ProductFilters\Livewire\ProductSearchComponent;
use Otomaties\ProductFilters\Livewire\ProductSortingComponent;

class ProductFiltersServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('product-filters::filters', function () {
            return collect(config('product-filters.filters'))
                ->map(function ($filter, $slug) {
                    return FilterFactory::create($filter['component'], $slug, $filter);
                });
        });

        $this->mergeConfigFrom(
            __DIR__.'/../../config/product-filters.php',
            'product-filters'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/product-filters.php' => $this->app->configPath('product-filters.php'),
        ], 'product-filters-config');

        $this->publishes([
            __DIR__.'/../../resources/views' => $this->app->resourcePath('views/vendor/product-filters'),
        ], 'product-filters-views');

        $this->loadViewsFrom(
            __DIR__.'/../../resources/views',
            'product-filters',
        );

        remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

        Livewire::component('product-filters', ProductFiltersComponent::class);
        Livewire::component('products', ProductsComponent::class);
        Livewire::component('product-filter-select', SelectComponent::class);
        Livewire::component('product-filter-checkbox', CheckboxComponent::class);
        Livewire::component('product-filter-radio', RadioComponent::class);
        Livewire::component('product-filter-price', PriceComponent::class);
        Livewire::component('product-sorting', ProductSortingComponent::class);
        Livewire::component('product-search', ProductSearchComponent::class);
    }
}
