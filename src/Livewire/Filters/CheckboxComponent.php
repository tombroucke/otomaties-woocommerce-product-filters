<?php

namespace Otomaties\ProductFilters\Livewire\Filters;

use Livewire\Component;

class CheckboxComponent extends Component
{
    public $options;

    public $value = [];

    public $slug;

    public $title;

    public function mount($filter)
    {
        $this->options = $filter->options();
        $this->value = $filter->value() ?? [];
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
        return view('ProductFilters::livewire.components.checkbox');
    }
}
