<?php

namespace Otomaties\ProductFilters\Livewire\Filters;

use Livewire\Component;

class PriceComponent extends Component
{
    public $min;

    public $max;

    public $title;

    public $slug;

    public function mount($filter)
    {
        $this->title = $filter->title();
    }

    public function updated()
    {
        $this->dispatch('filter-updated', $this->slug, $this->min, $this->max);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('product-filters::livewire.components.price');
    }
}
