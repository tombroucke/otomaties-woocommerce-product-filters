# Woocommerce Product Filters

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
        'custommeta' => [
            'title' => 'Custom meta',
            'component' => 'checkbox',
            'type' => 'meta',
            'meta_key' => 'custom_meta',
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
        'price' => [
            'title' => 'Price',
            'component' => 'price',
        ],
    ],
];
```

### Include livewire components

In `archive-product.blade.php`

```blade
<livewire:products />
```

### Optimize clear

`wp acorn optimize:clear`

### Optional: publish views

`wp acorn vendor:publish --tag="product-filters-views"`
