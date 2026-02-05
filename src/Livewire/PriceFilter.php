<?php

namespace Otomaties\ProductFilters\Livewire;

class PriceFilter extends Filter
{
    public mixed $value = [
        'min' => null,
        'max' => null,
    ];

    public function render()
    {
        return view('product-filters::livewire.filters.price-filter');
    }

    protected function queryString()
    {
        return [
            'value.min' => [
                'as' => 'price_min',
            ],
            'value.max' => [
                'as' => 'price_max',
            ],
        ];
    }
}
