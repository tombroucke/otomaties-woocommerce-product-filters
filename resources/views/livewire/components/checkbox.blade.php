<div class="products__filters__checkbox">
  @unless ($options->isEmpty())
    <label>{!! $title !!}</label>
    @foreach ($options as $key => $option)
      <div>
        <input
          id="{{ $slug }}_{{ $key }}"
          type="checkbox"
          value="{{ $key }}"
          wire:model.live="value"
        >
        <label for="{{ $slug }}_{{ $key }}">
          {{ $option }}
        </label>
      </div>
    @endforeach
  @endunless
</div>
