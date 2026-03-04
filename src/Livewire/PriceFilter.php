<?php

namespace Otomaties\ProductFilters\Livewire;

use Livewire\Attributes\On;
use Otomaties\ProductFilters\Filters\PriceFilter as FiltersPriceFilter;

class PriceFilter extends Filter
{
    public $upperLimit;

    public $lowerLimit;

    public mixed $value = [
        'min' => null,
        'max' => null,
    ];

    public function mount(string $filterKey, string $title, array $queriedObject, array $filteredProductQueryArgs, ?array $data = null)
    {
        parent::mount($filterKey, $title, $queriedObject, $filteredProductQueryArgs, $data);
        $this->loadLimits();
    }

    public function loadLimits()
    {
        $limits = FiltersPriceFilter::limits($this->queriedObject, $this->filteredProductQueryArgs);
        $this->lowerLimit = $limits['lower_limit'];
        $this->upperLimit = $limits['upper_limit'];
    }

    public function updatedFilteredProductQueryArgs()
    {
        $this->loadLimits();
    }

    #[On('filters-updated')]
    public function refreshOptions(): void
    {
        $this->loadLimits();
    }

    public function render()
    {
        return view('product-filters::livewire.filters.price-filter');
    }

    protected function queryString()
    {
        return [
            'value.min' => [
                'as' => 'price_min',
            ],
            'value.max' => [
                'as' => 'price_max',
            ],
        ];
    }
}
