<?php

namespace Otomaties\ProductFilters\Livewire;

class RadioFilter extends Filter
{
    public $options = [];

    public $taxonomy;

    public function mount(string $filterKey, string $title, array $queriedObject, array $filteredProductQueryArgs, ?array $data = null)
    {
        parent::mount($filterKey, $title, $queriedObject, $filteredProductQueryArgs, $data);
        $this->loadOptions();
    }

    public function loadOptions()
    {
        $this->options = [
            'eerste-optie' => 'Eerste optie',
            'tweede-optie' => 'Tweede optie',
        ];
    }

    public function render()
    {
        return view('product-filters::livewire.filters.radio-filter');
    }
}
