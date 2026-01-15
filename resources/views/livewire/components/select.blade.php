<div class="products__filters__select">
  <h3 for="filters_{{ $slug }}">{!! $title !!}</h3>
  <select
    id="filters_{{ $slug }}"
    name="filter_{{ $slug }}"
    wire:model.live="value"
  >
    <option value="">{!! $title !!}</option>
    @foreach ($options as $key => $option)
      <option value="{{ $key }}">
        {{ $option['label'] }} ({{ $option['count'] }})
      </option>
    @endforeach
  </select>
</div>
