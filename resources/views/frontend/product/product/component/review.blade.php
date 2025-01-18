@php
    $totalReviews = $product->reviews()->count();
    $totalRate = number_format($product->reviews()->avg('score'), 1);
    $starPercent = ($totalReviews == 0) ? '0' : $totalRate / 5 * 100;

    $fiveStar = $product->reviews()->where('score', 5)->count();
    $fourStar = $product->reviews()->where('score', 4)->count();
    $threeStar = $product->reviews()->where('score', 3)->count();
    $twoStar = $product->reviews()->where('score', 2)->count();
    $oneStar = $product->reviews()->where('score', 1)->count();
@endphp

<div class="review-container">
    <div class="panel-head">
        <div class="review-heading">Đánh giá sản phẩm</div>
        <div class="review-statistic">
            <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle">
                <div class="uk-width-large-1-3">
                    <div class="review-averate review-item">
                        <div class="title">Đánh giá trung bình</div>
                        <div class="score">{{ $totalRate }}/5</div>
                        <div class="star-rating" style="--star-width: {{ $starPercent }}%">
                            <div class="stars"></div>
                        </div>
                        <div class="total-rate">{{ $totalReviews }} đánh giá</div>
                    </div>
                </div>
                <div class="uk-width-large-1-3">
                    <div class="progress-block review-item">
                        @for ($i = 5; $i >= 1; $i--)
                            @php
                                $countStar = $product->reviews()->where('score', $i)->count();
                                $starPercentProgress = ($countStar > 0) ? $countStar / $totalReviews * 100 : 0
                            @endphp
                            <div class="progress-item">
                                <div class="uk-flex uk-flex-middle">
                                    <span class="text">{{ $i }}</span>
                                    <i class="fa fa-star"></i>
                                    <div class="uk-progress">
                                        <div class="uk-progress-bar" style="width: {{ $starPercentProgress }}%"></div>
                                    </div>
                                    <span class="text">{{ $countStar }}</span>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="uk-width-large-1-3">
                    <div class="review-action review-item">
                        <div class="text">Bạn đã dùng sản phẩm này?</div>
                        <button class="btn btn-review" data-uk-modal="{target: '#review'}">Gửi đánh giá</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="review-filter">
            <div class="uk-flex uk-flex-middle">
                <span class="filter-text">Lọc xem theo: </span>
                <div class="filter-item">
                    <span>Đã mua hàng</span>
                    <span>5 sao</span>
                    <span>4 sao</span>
                    <span>3 sao</span>
                    <span>2 sao</span>
                    <span>1 sao</span>
                </div>
            </div>
        </div>
        <div class="review-wrapper">
            @if (!empty($product->reviews) && count($product->reviews) > 0)
                @foreach ($product->reviews as $review)
                    @php
                        $avatar = getReviewName($review->fullname);
                        $name = $review->fullname;
                        $email = $review->email;
                        $phone = $review->phone;
                        $description = $review->description;
                        $rating = generateStar($review->score);
                        $created_at = $review->created_at;
                    @endphp
                    <div class="review-block-item">
                        <div class="review-general uk-clearfix">
                            <div class="review-avatar">
                                <span class="shae">{{ $avatar }}</span>
                            </div>
                            <div class="review-content-block">
                                <div class="review-content">
                                    <div class="name uk-flex uk-flex-middle">
                                        <span>{{ $name }}</span>
                                        <div class="review-buy">
                                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                                            Đã mua hàng tại {{ $system['homepage_brand'] }}
                                        </div>
                                    </div>
                                    {{-- <div class="review-star">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                    </div> --}}
                                    {!! $rating !!}
                                    <div class="description">
                                        {{ $description }}
                                    </div>
                                    <div class="review-toolbox">
                                        <div class="uk-flex uk-flex-middle">
                                            <div class="created_at">Ngày: {{ convertDateTime($created_at) }}</div>
                                            {{-- <div class="review-reply">Trả lời</div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="review-block-item uk-clearfix reply-block">
                        <div class="review-avatar">
                            <span class="shae">LV</span>
                        </div>
                        <div class="review-content-block">
                            <div class="review-content">
                                <div class="name uk-flex uk-flex-middle">
                                    <span>Thiều Lê Dũng</span>
                                    <div class="review-buy">
                                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                                        Đã mua hàng tại {{ $system['homepage_brand'] }}
                                    </div>
                                </div>
                                <div class="review-star">
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                </div>
                                <div class="description">
                                    CellphoneS xin chào anh Khang,
    Apple MacBook Air M1 256GB 2020 I Chính hãng Apple Việt Nam XÁM có giá thời điểm hiện tại (giá có thể thay đổi liên tục) là: 17.590.000đ (miền Nam) hiện đang sẵn hàng tại 21 Quang Trung, P. Hội Thương, TP. Pleiku, Gia Lai ạ.
    Để tránh hết hàng mình cho em xin số điện thoại và địa chỉ để em lên đơn giữ hàng trong 24h tại cửa hàng gần nhất cho mình nhé.Thông tin đến anh!
                                </div>
                                <div class="review-toolbox">
                                    <div class="uk-flex uk-flex-middle">
                                        <div class="created_at">Ngày: 12/09/2004</div>
                                        <div class="review-reply">Trả lời</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                @endforeach
            @endif
        </div>
    </div>
</div>

<div id="review" class="uk-modal">
    <div class="uk-modal-dialog modal-width-800">
        <a class="uk-modal-close uk-close"></a>
        <div class="review-popup-wrapper">
            <div class="panel-head">Đánh giá sản phẩm</div>
            <div class="panel-body">
                <div class="product-review">
                    <span class="image img-scaledown"><img src="{{ $product->image }}" alt="{{ $product->image }}"></span>
                    <div class="product-title uk-text-center">{{ $product->name }}</div>
                    <div class="popup-rating uk-clearfix uk-text-center">
                        <div class="rate uk-clearfix">
                            <input type="radio" id="star5" name="rate" class="rate" value="5" />
                            <label for="star5" title="Tuyệt vời">5 stars</label>
                            <input type="radio" id="star4" name="rate" class="rate" value="4" />
                            <label for="star4" title="Hài lòng">4 stars</label>
                            <input type="radio" id="star3" name="rate" class="rate" value="3" />
                            <label for="star3" title="Bình thường">3 stars</label>
                            <input type="radio" id="star2" name="rate" class="rate" value="2" />
                            <label for="star2" title="Tạm được">2 stars</label>
                            <input type="radio" id="star1" name="rate" class="rate" value="1" />
                            <label for="star1" title="Không thích">1 star</label>
                        </div>
                        <div class="rate-text uk-hidden">
                            không thích
                        </div>
                    </div>
                    <div class="review-form">
                        <div action="" class="uk-form form">
                            <div class="form-row">
                                <textarea name="" id="" class="review-textarea" placeholder="Hãy chia sẻ cảm nhận của bạn về sản phẩm ..."></textarea>
                            </div>
                            <div class="form-row">
                                <div class="uk-flex uk-flex-middle">
                                    <div class="gender-item uk-flex uk-flex-middle">
                                        <input type="radio" name="gender" class="gender" value="male" id="male">
                                        <label for="male">Nam</label>
                                    </div>
                                    <div class="gender-item uk-flex uk-flex-middle">
                                        <input type="radio" name="gender" class="gender" value="female" id="female">
                                        <label for="female">Nữ</label>
                                    </div>
                                </div>
                            </div>  
                            <div class="uk-grid uk-grid-medium">
                                <div class="uk-width-large-1-2">
                                    <div class="form-row">
                                        <input type="text" name="fullname" value="" class="review-text" placeholder="Nhập vào họ tên">
                                    </div>
                                </div>
                                <div class="uk-width-large-1-2">
                                    <div class="form-row">
                                        <input type="text" name="phone" value="" class="review-text" placeholder="Nhập vào số điện thoại">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <input type="text" name="email" value="" class="review-text" placeholder="Nhập vào email">
                            </div>
                            <div class="uk-text-center">
                                <button type="submit" value="send" class="btn-send-review" name="create">Hoàn tất</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" class="reviewable_type" value="{{ $reviewable }}">
<input type="hidden" class="reviewable_id" value="{{ $product->id }}">
<input type="hidden" class="review_parent_id" value="0">