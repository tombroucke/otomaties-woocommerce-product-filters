<?php

namespace Otomaties\ProductFilters\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Otomaties\ProductFilters\Livewire\Filters\CheckboxComponent;
use Otomaties\ProductFilters\Livewire\Filters\SelectComponent;
use Otomaties\ProductFilters\Livewire\ProductFiltersComponent;
use Otomaties\ProductFilters\Livewire\ProductsComponent;
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
        $this->app->bind('ProductFilters::filter', function ($app, $params) {
            $filter = $params['filter'];
            $slug = $params['slug'];
            unset($filter['slug']);

            $class = 'Otomaties\ProductFilters\Filters\\'.Str::studly($filter['component']).'\\'.Str::studly($filter['type']).Str::studly($filter['component']);

            return new $class($slug, $filter);
        });

        $this->app->singleton('ProductFilters::filters', function () {
            return collect(config('product-filters.filters'))
                ->map(function ($filter, $slug) {
                    return app('ProductFilters::filter', ['filter' => $filter, 'slug' => $slug]);
                })
                ->values();
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

        $this->loadViewsFrom(
            __DIR__.'/../../resources/views',
            'ProductFilters',
        );

        remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

        Livewire::component('product-filters', ProductFiltersComponent::class);
        Livewire::component('products', ProductsComponent::class);
        Livewire::component('product-filter-select', SelectComponent::class);
        Livewire::component('product-filter-checkbox', CheckboxComponent::class);
        Livewire::component('product-sorting', ProductSortingComponent::class);
    }
}
