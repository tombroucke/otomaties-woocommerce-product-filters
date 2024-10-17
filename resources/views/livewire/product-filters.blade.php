<div>
  @foreach ($filters as $filter)
    @livewire('product-filter-' . $filter->componentName(), ['slug' => $filter->slug(), 'filter' => $filter])
  @endforeach
</div>
