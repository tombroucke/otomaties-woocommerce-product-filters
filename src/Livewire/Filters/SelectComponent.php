<?php

namespace Otomaties\ProductFilters\Livewire\Filters;

use Livewire\Component;

class SelectComponent extends Component
{
    public $options;

    public $value;

    public $slug;

    public $title;

    public $queryString;

    public function mount($filter)
    {
        $this->options = $filter->options();
        $this->title = $filter->title();
    }

    public function updated()
    {
        $this->dispatch('filter-updated', $this->slug, $this->value);
    }

    protected function queryString()
    {
        return [
            'value' => [
                'as' => $this->slug,
            ],
        ];
    }

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
