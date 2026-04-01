<?php

namespace Otomaties\ProductFilters\Livewire;

use Livewire\Attributes\Url;

class SearchFilter extends Filter
{
    #[Url]
    public mixed $value = '';

    public function render()
    {
        return view('product-filters::livewire.filters.search');
    }

    protected function queryString()
    {
        return [
            'value' => [
                'as' => 's',
            ],
        ];
    }
}
