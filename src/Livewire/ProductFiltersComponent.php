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
        return view('ProductFilters::livewire.product-filters', [
            'filters' => app('ProductFilters::filters'),
        ]);
    }
}
