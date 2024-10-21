<div>
  <select wire:model.live="selected">
    @foreach ($options as $key => $option)
      <option value="{{ $key }}">
        {{ $option }}
      </option>
    @endforeach
  </select>
</div>
