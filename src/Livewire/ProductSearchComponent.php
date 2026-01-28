<?php

namespace Otomaties\ProductFilters\Livewire;

use Livewire\Attributes\Url;
use Livewire\Component;

class ProductSearchComponent extends Component
{
    #[Url('product_query')]
    public $query = '';

    public function mount() {}

    public function updated()
    {
        $this->dispatch('search-updated', $this->query);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('product-filters::livewire.product-search');
    }

    public function resetSearch()
    {
        $this->query = '';
        $this->updated();
    }
}
