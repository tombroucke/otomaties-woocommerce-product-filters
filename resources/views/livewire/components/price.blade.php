<div class="products__filters__price">
  <label>{!! $title !!}</label>
  <div>
    <div>
      <input
        type="number"
        wire:model.live="min"
        placeholder="Min"
      >
    </div>
    <div>
      <input
        type="number"
        wire:model.live="max"
        placeholder="Max"
      >
    </div>
  </div>
</div>
