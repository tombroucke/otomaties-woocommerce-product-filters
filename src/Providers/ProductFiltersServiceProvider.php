<?php

namespace Otomaties\ProductFilters\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Otomaties\ProductFilters\Livewire\CheckboxFilter;
use Otomaties\ProductFilters\Livewire\PriceFilter;
use Otomaties\ProductFilters\Livewire\ProductsComponent;
use Otomaties\ProductFilters\Livewire\RadioFilter;
use Otomaties\ProductFilters\Livewire\SelectFilter;

class ProductFiltersServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/product-filters.php',
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
            __DIR__ . '/../../config/product-filters.php' => $this->app->configPath('product-filters.php'),
        ], 'product-filters-config');

        $this->publishes([
            __DIR__ . '/../../resources/views' => $this->app->resourcePath('views/vendor/product-filters'),
        ], 'product-filters-views');

        $this->loadViewsFrom(
            __DIR__ . '/../../resources/views',
            'product-filters',
        );

        remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

        Blade::directive('woocommerce_product', function ($expression) {
            return "<?php
                \$GLOBALS['post'] = get_post({$expression});
                setup_postdata(\$GLOBALS['post']);
            ?>";
        });

        Blade::directive('endwoocommerce_product', function () {
            return '<?php wp_reset_postdata(); ?>';
        });

        Livewire::component('products', ProductsComponent::class);
        Livewire::component('product-filter.select', SelectFilter::class);
        Livewire::component('product-filter.checkbox', CheckboxFilter::class);
        Livewire::component('product-filter.radio', RadioFilter::class);
        Livewire::component('product-filter.price', PriceFilter::class);
    }
}
