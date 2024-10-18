<div>
  @foreach ($filters as $filter)
    @livewire('product-filter-' . $filter->component(), ['slug' => $filter->slug(), 'filter' => $filter])
  @endforeach
</div>
