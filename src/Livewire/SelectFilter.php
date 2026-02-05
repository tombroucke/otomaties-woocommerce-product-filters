<?php

namespace Otomaties\ProductFilters\Livewire;

use Livewire\Attributes\On;
use Otomaties\ProductFilters\Filters\MetaFilter;
use Otomaties\ProductFilters\Filters\TaxonomyFilter;

class SelectFilter extends Filter
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
        switch ($this->data['type']) {
            case 'taxonomy':
                $this->options = TaxonomyFilter::options($this->data['taxonomy'], $this->queriedObject, $this->filteredProductQueryArgs);
                break;
            case 'meta':
                $this->options = MetaFilter::options($this->data['meta_key'], $this->queriedObject, $this->filteredProductQueryArgs);
                break;
            default:
                $this->options = [];
                break;
        }
    }

    public function updatedFilteredProductQueryArgs()
    {
        $this->loadOptions();
    }

    #[On('filters-updated')]
    public function refreshOptions(): void
    {
        $this->loadOptions();
    }

    public function render()
    {
        return view('product-filters::livewire.filters.select-filter');
    }

    protected function queryString()
    {
        return [
            'value' => [
                'as' => $this->filterKey,
            ],
        ];
    }
}
