# Woocommerce Product Filters

> [!WARNING]
> This project is not production ready. There is more work that needs to be done
>
> - Enable / disable filters on certain pages
> - Add more components (radio, price range)
> - Only fetch terms / meta values from products that are currently eligible

## Installation

### Require package

`composer require tombroucke/otomaties-woocommerce-product-filters`

### Generate key

`wp acorn key:generate`

### Add livewire styles and script

```php
add_filter('wp_head', function () {
    echo Blade::render('@livewireStyles');
});

add_filter('wp_footer', function () {
    echo Blade::render('@livewireScripts');
});
```

### Publish config file

`wp acorn vendor:publish --tag="product-filters-config"`

### Update config file

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ProductFilters Package
    |--------------------------------------------------------------------------
    */

    'filters' => [
        'product_category' => [
            'title' => 'Product Category',
            'component' => 'checkbox',
            'type' => 'taxonomy',
            'taxonomy' => 'product_cat',
        ],
        'features' => [
            'title' => 'Features',
            'component' => 'select',
            'type' => 'taxonomy',
            'taxonomy' => 'pa_features',
        ],
        'size' => [
            'title' => 'Size',
            'component' => 'select',
            'type' => 'taxonomy',
            'taxonomy' => 'pa_size',
        ],
    ],
];
```

### Include livewire components

In `archive-product.blade.php`

```blade
<div class="row">
  <div class="col-12">
    <livewire:product-sorting />
  </div>
  <div class="col-md-4">
    <livewire:product-filters />
  </div>
  <div class="col-md-8">
    <livewire:products />
  </div>
</div>
```

### Optimize clear (optional if you run into an issue)
`wp acorn optimize:clear`
