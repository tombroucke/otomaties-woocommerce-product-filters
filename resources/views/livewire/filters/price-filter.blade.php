<div>
  <h4 class="mb-2 font-semibold">{{ $title }}</h4>
  <div class="flex gap-2">
    <input
      class="w-1/2 rounded border p-2"
      type="number"
      wire:model.live.debounce.500ms="value.min"
      placeholder="Min"
    >
    <input
      class="w-1/2 rounded border p-2"
      type="number"
      wire:model.live.debounce.500ms="value.max"
      placeholder="Max"
    >
  </div>
</div>
