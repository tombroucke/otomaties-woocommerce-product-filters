<?php

namespace Otomaties\ProductFilters\Livewire\Filters;

use Livewire\Component;

class PriceComponent extends Component
{
    public $min;

    public $max;

    public string $title;

    public string $slug;

    public function updated()
    {
        $min = $this->min;
        $max = $this->max;
        $this->dispatch('filter-updated', $this->slug, compact('min', 'max'));
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

    protected function queryString()
    {
        return [
            'min' => [
                'as' => 'price_min',
            ],
            'max' => [
                'as' => 'price_max',
            ],
        ];
    }
}
