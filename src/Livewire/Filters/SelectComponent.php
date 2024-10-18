<?php

namespace Otomaties\ProductFilters\Livewire\Filters;

class SelectComponent extends FilterComponent
{
    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('product-filters::livewire.components.select');
    }
}
