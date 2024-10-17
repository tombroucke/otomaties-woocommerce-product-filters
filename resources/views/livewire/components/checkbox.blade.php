<div>
  <label>{!! $title !!}</label>
  @foreach ($options as $key => $option)
    <div class="form-check">
      <input
        class="form-check-input"
        id="{{ $slug }}_{{ $key }}"
        type="checkbox"
        value="{{ $key }}"
        wire:model.live="value"
      >
      <label
        class="form-check-label"
        for="{{ $slug }}_{{ $key }}"
      >
        {{ $option }}
      </label>
    </div>
  @endforeach
</div>
