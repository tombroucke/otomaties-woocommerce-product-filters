<div class="products__filters__select">
  @unless (empty($options))
    <h3>{!! $title !!}</h3>
    <select
      id="{{ $filterKey }}"
      name="{{ $filterKey }}"
      wire:model.live="value"
    >
      <option value="">{!! __('Select') !!}</option>
      @foreach ($options as $key => $option)
        <option value="{{ $key }}">
          {{ $option['label'] }} ({{ $option['count'] }})
        </option>
      @endforeach
    </select>
  @endunless
</div>
