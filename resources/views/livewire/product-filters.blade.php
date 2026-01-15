<div>
  @foreach ($filters as $filter)
    @livewire(
        'product-filter-' . $filter->component(),
        [
            'slug' => $filter->slug(),
            'title' => $filter->title(),
            'filter' => $filter,
            'filterValues' => $filterValues,
        ],
        key('filter-' . $filter->slug())
    )
  @endforeach
</div>
