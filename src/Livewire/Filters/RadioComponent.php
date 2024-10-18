<?php

namespace Otomaties\ProductFilters\Livewire\Filters;

class RadioComponent extends FilterComponent
{
    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('product-filters::livewire.components.radio');
    }
}
