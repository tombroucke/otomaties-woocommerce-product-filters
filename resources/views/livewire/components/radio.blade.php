<div class="products__filters__radio">
  @unless (empty($options))
    <h3>{!! $title !!}</h3>
    @foreach ($options as $key => $option)
      <div>
        <input
          id="{{ $slug }}_{{ $key }}"
          type="radio"
          value="{{ $key }}"
          wire:model.live="value"
        >
        <label for="{{ $slug }}_{{ $key }}">
          {{ $option['label'] }} ({{ $option['count'] }})
        </label>
      </div>
    @endforeach
  @endunless
</div>
