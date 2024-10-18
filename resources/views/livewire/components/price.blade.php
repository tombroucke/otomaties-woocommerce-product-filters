<div>
  <label>{!! $title !!}</label>
  <div class="d-flex">
    <div>
      <input
        class="form-control"
        type="number"
        wire:model.live="min"
        placeholder="Min"
      >
    </div>
    <div>
      <input
        class="form-control"
        type="number"
        wire:model.live="max"
        placeholder="Max"
      >
    </div>
  </div>
</div>
