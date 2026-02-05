<?php

namespace Otomaties\ProductFilters\Livewire;

use Livewire\Attributes\Reactive;
use Livewire\Component;

abstract class Filter extends Component
{
    public string $filterKey;

    public string $title;

    #[Reactive]
    public array $queriedObject;

    #[Reactive]
    public array $filteredProductQueryArgs;

    public ?array $data;

    public mixed $value;

    public function mount(string $filterKey, string $title, array $queriedObject, array $filteredProductQueryArgs, ?array $data = null)
    {
        $this->filterKey = $filterKey;
        $this->title = $title;
        $this->queriedObject = $queriedObject;
        $this->filteredProductQueryArgs = $filteredProductQueryArgs;
        $this->data = $data;
    }

    public function updatedValue()
    {
        $this->dispatch('filter-updated', [
            'key' => $this->filterKey,
            'value' => $this->value,
        ]);
    }
}
