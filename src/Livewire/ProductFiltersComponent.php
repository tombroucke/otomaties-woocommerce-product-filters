<?php

namespace Otomaties\ProductFilters\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class ProductFiltersComponent extends Component
{
    public array $filterValues = [];

    public function mount()
    {
        $this->filterValues = $this->filterValuesFromQuery();
    }

    private function filterValuesFromQuery(): array
    {
        $queryParams = request()->query();
        $filters = app('product-filters::filters');

        return collect($queryParams)
            ->map(function ($value, $key) use ($filters) {
                $filter = $filters->get($key);
                if ($filter) {
                    return is_array($value) ? $value : [$value];
                } elseif ($key === 'price_min' || $key === 'price_max') {
                    return $value;
                }

                return null;
            })
            ->filter()
            ->toArray();
    }

    #[On('filter-updated')]
    public function applyFilters($slug, $value)
    {
        $this->filterValues[$slug] = $value;

        $this->dispatch('filters-applied', $this->filterValues);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('product-filters::livewire.product-filters', [
            'filters' => app('product-filters::filters'),
            'filterValues' => $this->filterValues,
        ]);
    }
}
