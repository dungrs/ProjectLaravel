<div class="filter-content">
    <div class="filter-overlay">
        <div class="filter-close">
            <i class="fi fi-rs-cross"></i>
        </div>
        <div class="filter-content-container">
            @if (!is_null($filters))
                @foreach ($filters as $key => $val)
                    <div class="filter-item">
                        <div class="filter-heading">{{ $val->name }}</div>
                        @if (count($val->attributes))
                            <div class="filter-body">
                                @foreach ($val->attributes as $index => $item)
                                    <div class="filter-choose">
                                        <input type="checkbox" id="attribute-{{ $item->attribute_id }}" class="input-checkbox filtering">
                                        <label for="attribute-{{ $item->attribute_id }}" class="">{{ $item->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
            <div class="filter-item filter-price slider-box">
                <div class="filter-heading" for="priceRange">Lọc Theo Giá:</div>
                <div class="filter-price-content">
                    <input type="text" id="priceRange" readonly class="uk-hidden">
                    <div id="price-range" class="slider ui-slider ui-slider-horizontal ui-widget"></div>
                    <div class="filter-input-value mt5">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <input type="text" class="min-value input-value" value="0">
                            <input type="text" class="max-value input-value" value="10.000.000">
                        </div>
                    </div>
                </div>
            </div>
            <div class="filter-item filter-category">
                <div class="filter-heading">Tình trạng sản phẩm</div>
                <div class="filter-body">
                    <div class="filter-choose">
                        <input id="input-available" type="checkbox" name="stock[]" value="1" class="input-checkbox">
                        <label for="input-available">Còn hàng</label>
                    </div>
                    <div class="filter-choose">
                        <input id="input-outstock" type="checkbox" name="stock[]" value="0" class="input-checkbox">
                        <label for="input-outstock">Hết hàng</label>
                    </div>
                </div>
            </div>
            <div class="filter-item filter-category">
                <div class="filter-heading">Lọc theo đánh giá</div>
                <div class="filter-body">
                    <div class="filter-choose uk-flex uk-flex-middle">
                        <input id="input-rate-5" type="checkbox" name="rate[]" value="5" class="input-checkbox">
                        <label for="input-rate-5" class="uk-flex uk-flex-middle">
                            <div class="filter-star">
                                <i class="fi-rs-star"></i>
                                <i class="fi-rs-star"></i>
                                <i class="fi-rs-star"></i>
                                <i class="fi-rs-star"></i>
                                <i class="fi-rs-star"></i>
                            </div>
                        </label>
                        <span class="totalProduct ml5 mb5">(5)</span>
                    </div>
                    <div class="filter-choose uk-flex uk-flex-middle">
                        <input id="input-rate-5" type="checkbox" name="rate[]" value="5" class="input-checkbox">
                        <label for="input-rate-5" class="uk-flex uk-flex-middle">
                            <div class="filter-star">
                                <i class="fi-rs-star"></i>
                                <i class="fi-rs-star"></i>
                                <i class="fi-rs-star"></i>
                                <i class="fi-rs-star"></i>
                            </div>
                        </label>
                        <span class="totalProduct ml5 mb5">(5)</span>
                    </div>
                    <div class="filter-choose uk-flex uk-flex-middle">
                        <input id="input-rate-5" type="checkbox" name="rate[]" value="5" class="input-checkbox">
                        <label for="input-rate-5" class="uk-flex uk-flex-middle">
                            <div class="filter-star">
                                <i class="fi-rs-star"></i>
                                <i class="fi-rs-star"></i>
                                <i class="fi-rs-star"></i>
                            </div>
                        </label>
                        <span class="totalProduct ml5 mb5">(5)</span>
                    </div>
                    <div class="filter-choose uk-flex uk-flex-middle">
                        <input id="input-rate-5" type="checkbox" name="rate[]" value="5" class="input-checkbox">
                        <label for="input-rate-5" class="uk-flex uk-flex-middle">
                            <div class="filter-star">
                                <i class="fi-rs-star"></i>
                                <i class="fi-rs-star"></i>
                            </div>
                        </label>
                        <span class="totalProduct ml5 mb5">(5)</span>
                    </div>
                    <div class="filter-choose uk-flex uk-flex-middle">
                        <input id="input-rate-5" type="checkbox" name="rate[]" value="5" class="input-checkbox">
                        <label for="input-rate-5" class="uk-flex uk-flex-middle">
                            <div class="filter-star">
                                <i class="fi-rs-star"></i>
                            </div>
                        </label>
                        <span class="totalProduct ml5 mb5">(5)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>