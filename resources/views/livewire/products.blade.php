<div class="products">
  <div class="products__results">
    {!! sprintf(
        /* translators: %s: Number of products found. */
        __('%s products found', 'otomaties-woocommerce-product-filters'),
        '<strong>' . number_format_i18n($this->foundProducts) . '</strong>',
    ) !!}
  </div>
  <div class="products__sorting">
    <select wire:model.live="orderBy">
      @foreach ($orderByOptions as $key => $orderByOption)
        <option value="{{ $key }}">
          {{ $orderByOption }}
        </option>
      @endforeach
    </select>
  </div>
  <div class="products__filters">
    @foreach ($filters as $key => $filter)
      @livewire(
          'product-filter.' . $filter['component'],
          [
              'filter-key' => $key,
              'title' => $filter['title'],
              'queriedObject' => $queriedObject,
              'filteredProductQueryArgs' => $this->filteredProductQueryArgs,
              'data' => $filter['data'] ?? null,
          ],
          key('filter-' . $key)
      )
    @endforeach
  </div>
  <div class="products__products">
    <div>
      @unless ($products->isEmpty())
        @php
          do_action('woocommerce_before_shop_loop');
          woocommerce_product_loop_start();
        @endphp

        @foreach ($products as $product)
          @woocommerce_product($product->get_ID())
            @include('woocommerce.content-product')
          @endwoocommerce_product
        @endforeach

        @php
          woocommerce_product_loop_end();
          do_action('woocommerce_after_shop_loop');
        @endphp
      @else
        @php
          do_action('woocommerce_no_products_found');
        @endphp
      @endunless

      @if (count($this->pages) > 1)
        <nav aria-label="{!! __('Product navigation', 'otomaties-woocommerce-product-filters') !!}">
          <ul class="pagination">
            @foreach ($this->pages as $page)
              <li @class(['page-item', 'active' => $page['active']])>
                <button
                  class="page-link"
                  wire:click="goToPage({{ $page['page'] }})"
                >
                  {!! $page['page'] !!}
                </button>
              </li>
            @endforeach
          </ul>
        </nav>
      @endif
    </div>
  </div>
</div>
