<?php

namespace Otomaties\ProductFilters\Livewire;

use Livewire\Component;

class ProductFiltersComponent extends Component
{
    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('product-filters::livewire.product-filters', [
            'filters' => app('product-filters::filters'),
        ]);
    }
}
