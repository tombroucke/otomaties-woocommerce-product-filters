<div>
  <input
    type="text"
    wire:model.live="query"
    placeholder="{{ __('Search products...', 'otomaties-woocommerce-product-filters') }}"
  >
  {{-- <button wire:click="resetSearch">
    {{ __('Reset', 'otomaties-woocommerce-product-filters') }}
  </button> --}}
</div>
