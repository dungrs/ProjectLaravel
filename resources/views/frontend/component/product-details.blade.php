@php
    $name = $product->name;
    $image = asset(image($product->image));
    $price = number_format($product->product_variants->first()->price ?? 0);
    $discount = [];
    if (isset($product->promotions)) {
        foreach ($product->promotions as $promotion) {
            $discount[] = getDiscountType($promotion);
        }
    }

    $review = getReview();
    $canonical = writeUrl($product->canonical, true, true);
    $description = $product->description;
    $attributeCatalogue = $product->attributeCatalogue;
    $album = json_decode($product->album);
@endphp
<div class="panel-body">
    <div class="uk-grid uk-grid-medium">
        <div class="uk-width-large-1-2">
            <div class="popup-gallery">
                <div class="swiper-container">
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-wrapper big-pic">
                        @foreach($album as $key => $val)
                        <div class="swiper-slide" data-swiper-autoplay="2000">
                            <a href="{{ $val }}" class="image img-cover"><img src="{{ $val }}" alt="{{ $val }}"></a>
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <div class="swiper-container-thumbs">
                    <div class="swiper-wrapper pic-list">
                        @foreach($album as $key => $val)
                        <div class="swiper-slide">
                            <span  class="image img-cover"><img src="{{ $val }}" alt="{{ $val }}"></span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="uk-width-large-1-2">
            <div class="popup-product">
                <h1 class="title">
                    <span>{{ $product->name }}</span>
                </h1>
                <div class="rating">
                    <div class="uk-flex uk-flex-middle">
                        <div class="author">Đánh giá</div>
                        <div class="star">
                            <?php for($i = 0; $i<=4; $i++){ ?>
                            <i class="fa fa-star"></i>
                            <?php }  ?>
                        </div>
                        <div class="rate-number">(65 reviews)</div>
                    </div>
                </div>
                <div class="price">
                    <div class="uk-flex uk-flex-bottom">
                        @if(!empty($discount))
                            <div class="price-sale">{{ $discount[0]['sale_price'] }} đ</div>
                            <div class="price-old">{{ $discount[0]['old_price'] }} đ</div>
                        @else
                            <div class="price-sale">{{ $price }} đ</div>
                        @endif
                    </div>
                </div>
                <div class="description">
                    {!! $description !!}
                </div>
                @if (!is_null($attributeCatalogue))
                    @foreach ($attributeCatalogue as $key => $val)
                        <div class="attribute">
                            <div class="attribute-item attribute-color">
                                <div class="label">{{ $val->name }}: </div>
                                @if (!is_null($val->attributes))
                                    <div class="attribute-value">
                                        @foreach ($val->attributes as $attr)
                                            <a data-attributeID="{{ $attr->id }}" title="{{ $attr->name }}">{{ $attr->name }}</a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div><!-- .attribute -->
                    @endforeach
                @endif
                <div class="quantity">
                    <div class="text">Quantity</div>
                    <div class="uk-flex uk-flex-middle">
                        <div class="quantitybox uk-flex uk-flex-middle">
                            <div class="minus quantity-button"><img src="resources/img/minus.svg" alt=""></div>
                            <input type="text" name="" value="1" class="quantity-text">
                            <div class="plus quantity-button"><img src="resources/img/plus.svg" alt=""></div>
                        </div>
                        <div class="btn-group uk-flex uk-flex-middle">
                            <div class="btn-item btn-1"><a href="" title="">Add To Cart</a></div>
                            <div class="btn-item btn-2"><a href="" title="">Buy Now</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>