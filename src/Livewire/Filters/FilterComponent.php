<?php

namespace Otomaties\ProductFilters\Livewire\Filters;

use Livewire\Component;

abstract class FilterComponent extends Component
{
    public $options;

    public $value = [];

    public $slug;

    public $title;

    public $filterSlug;

    public function mount($filter)
    {
        $this->filterSlug = $filter->slug();
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
}
