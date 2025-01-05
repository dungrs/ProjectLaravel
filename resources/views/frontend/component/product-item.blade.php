@php
    $name = $product->name;
    $image = asset(image($product->image));
    $price = number_format($product->product_variants->first()->price ?? 0); // Giá gốc
    if (isset($product->promotion)) {
        $promotion = $product->promotion->toArray();
        $discount = getDiscountType($promotion);
    }
    
    $review = getReview();
    $canonical = writeUrl($product->canonical, true, true)
@endphp

<div class="product-item product">
    @if (isset($product->promotion))
        <div class="badge badge-bg<?php echo rand(1,3) ?>">-{{ $discount['value'] }} {{ $discount['type'] }}</div>
    @endif
    <a href="{{ $canonical }}" class="image img-cover"><img src="{{ $image }}" alt=""></a>
    <div class="info">
        <div class="category-title"><a href="" title="">{{ isset($product_catalogue) ? $product_catalogue : '' }}</a></div>
        <h3 class="title"><a href="" title="">{{ $name }}</a></h3>
        <div class="rating">
            <div class="uk-flex uk-flex-middle">
                <div class="star">
                    @for ($j = 0; $j < $review['stars']; $j++)
                        <i class="fa fa-star"></i>
                    @endfor
                </div>
                <span class="rate-number">({{ $review['counts'] }})</span>
            </div>
        </div>
        <div class="product-group">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <div class="price uk-flex uk-flex-bottom">
                    @if(isset($product->promotion))
                        <div class="price-sale">{{ $discount['sale_price'] }} đ</div>
                        <div class="price-old">{{ $discount['old_price'] }} đ</div>
                    @else
                        <div class="price-sale">{{ $price }} đ</div>
                    @endif
                </div>
                <div class="addcart">
                    {!! renderQuickBuy($product, $canonical, $name) !!}
                </div>
            </div>
        </div>

    </div>
    <div class="tools">
        <a href="" title=""><img src="{{ asset("frontend/resources/img/trend.svg") }}" alt=""></a>
        <a href="" title=""><img src="{{ asset("frontend/resources/img/wishlist.svg") }}" alt=""></a>
        <a href="" title=""><img src="{{ asset("frontend/resources/img/compare.svg") }}" alt=""></a>
        <a href="#popup" data-uk-modal title=""><img src="{{ asset("frontend/resources/img/view.svg") }}" alt=""></a>
    </div>
</div>