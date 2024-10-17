<div>
  <label for="filters_{{ $slug }}">{!! $title !!}</label>
  <select
    class="form-select"
    id="filters_{{ $slug }}"
    name="filter_{{ $slug }}"
    wire:model.live="value"
  >
    <option value="">{!! $title !!}</option>
    @foreach ($options as $key => $option)
      <option value="{{ $key }}">
        {{ $option }}
      </option>
    @endforeach
  </select>
</div>
