<?php

namespace Otomaties\ProductFilters\Livewire\Filters;

use Livewire\Component;
use Livewire\Attributes\On;

abstract class FilterComponent extends Component
{
    public array $options;

    public string|array $value = [];

    public string $slug;

    public string $title;

    public ?string $queriedObjectTaxonomy = null;

    public ?int $queriedObjectTermId = null;

    public function mount($filter, $filterValues)
    {
        $queriedObject = get_queried_object();

        if ($queriedObject instanceof \WP_Term) {
            $this->queriedObjectTaxonomy = $queriedObject->taxonomy;
            $this->queriedObjectTermId = $queriedObject->term_id;
        }
        
        $this->options = $filter->options($this->queriedObjectTaxonomy, $this->queriedObjectTermId, $filterValues);
    }

    public function updated()
    {
        $this->dispatch('filter-updated', $this->slug, $this->value);
    }

    #[On('filters-applied')]
    public function onFilterUpdated($filterValues)
    {
        $this->options = app('product-filters::filters')
            ->get($this->slug)
            ->options($this->queriedObjectTaxonomy, $this->queriedObjectTermId, $filterValues);
    }

    protected function queryString()
    {
        return [
            'value' => [
                'as' => $this->slug,
            ],
        ];
    }
}
