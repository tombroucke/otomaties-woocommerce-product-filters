<div>
  @if ($productQuery->have_posts())
    @php
      do_action('woocommerce_before_shop_loop');
      woocommerce_product_loop_start();
    @endphp
    @while ($productQuery->have_posts())
      @php
        $productQuery->the_post();
        do_action('woocommerce_shop_loop');
        wc_get_template_part('content', 'product');
      @endphp
    @endwhile
    @php
      woocommerce_product_loop_end();
      do_action('woocommerce_after_shop_loop');
    @endphp
  @else
    @php
      do_action('woocommerce_no_products_found');
    @endphp
  @endif

  @unless (count($this->pages) <= 1)
    <nav>
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
  @endunless
</div>
