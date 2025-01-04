@extends('frontend.homepage.layout')
@section('content')
    @php
        $slideKeyword = app('App\\Classes\\SlideEnum')
    @endphp
    <div id="homepage" class="homepage">
        @include('frontend.component.slide')
        <div class="panel-category page-setup">
            <div class="uk-container uk-container-center">
                @if (isset($widget['category-hl']))
                    <div class="panel-head">
                        <div class="uk-flex uk-flex-middle">
                            <h2 class="heading-1"><span>{{ $widget['category-hl']['name'] }}</span></h2>
                            <div class="category-children">
                                <ul class="uk-list uk-clearfix uk-flex uk-flex-middle">
                                    @include('frontend.component.catalogue', ['category' => $widget['category-hl']['object']])
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                @if (isset($widget['category']))
                    <div class="panel-body">
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                @foreach ($widget['category']['object'] as $category)
                                    <div class="swiper-slide">
                                        <div class="category-item bg-<?php echo rand(1,7) ?>">
                                            <a href="" class="image img-scaledown img-zoomin"><img src="{{ asset($category->image) }}" alt=""></a>
                                            <div class="title"><a href="{{ writeUrl($category->canonical) }}" title="">{{ $category->name }}</a></div>
                                            <div class="total-product">{{ $category->product_count }} sản phẩm</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @if (count($slides[$slideKeyword::BANNER]['item']))
        <div class="panel-banner">
            <div class="uk-container uk-container-center">
                <div class="panel-body">
                    <div class="uk-grid uk-grid-medium">
                        @foreach ($slides[$slideKeyword::BANNER]['item'] as $key => $val)
                            @php
                                $name = $val['name'];
                                $description = $val['description'];
                                $image = $val['image'];
                                $canonical = writeUrl($val['canonical'], true, true)
                            @endphp
                            <div class="uk-width-large-1-3">
                                <div class="banner-item">
                                    <span class="image"><img src="{{ asset($image) }}"</span>
                                    <div class="banner-overlay">
                                        <div class="banner-title">{{ $description }}</div>
                                        <a class="btn-shop" href="{{ $canonical }}">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if (isset($widget['category-home']))
            @foreach ($widget['category-home']['object'] as $object)
            <div class="panel-popular">
                <div class="uk-container uk-container-center">
                    <div class="panel-head">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <h2 class="heading-1"><span>{{ $object->name }}</span></h2>
                            @include('frontend.component.catalogue', ['category' => $object->children])
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="uk-grid uk-grid-medium">
                            @foreach ($object->children as $children)
                                @if (!empty($children->productLists))
                                    @foreach ($children->productLists as $product)
                                        <div class="uk-width-large-1-5 mb20">
                                            @include('frontend.component.product-item', ['product' => $product, 'product_catalogue' => $children->name])
                                        </div>
                                    @endforeach
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
        @if (isset($widget['bestseller']))
            <div class="panel-bestseller">
                <div class="uk-container uk-container-center">
                    <div class="panel-head">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <h2 class="heading-1"><span>{{ $widget['bestseller']['name'] }}</span></h2>
                            <div class="category-children">
                                <ul class="uk-list uk-clearfix uk-flex uk-flex-middle">
                                    @include('frontend.component.catalogue', ['category' => $widget['bestseller']['object']])
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="uk-grid uk-grid-medium">
                            <div class="uk-width-large-1-4">
                                <div class="best-seller-banner">
                                    @php
                                        $image = asset(image($widget['bestseller']['album'][0]));
                                    @endphp
                                    <a href="" class="image img-cover"><img src="{{ $image }}" alt=""></a>
                                    <div class="banner-title" style="color: #fff">{!! json_decode($widget['bestseller']['description'], true)["1"]  !!}</div>
                                </div>
                            </div>
                            <div class="uk-width-large-3-4">
                                @if (isset($widget['bestseller']['object']))
                                    <div class="product-wrapper">
                                        <div class="swiper-button-next"></div>
                                        <div class="swiper-button-prev"></div>
                                        <div class="swiper-container">
                                            <div class="swiper-wrapper">
                                                @foreach ($widget['bestseller']['object'] as $product)
                                                    <div class="swiper-slide">
                                                        @include('frontend.component.product-item', ['product' => $product])
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="panel-deal page-setup">
            <div class="uk-container uk-container-center">
                <div class="panel-head">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <h2 class="heading-1"><span>Giảm giá trong ngày</span></h2>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="uk-grid uk-grid-medium">
                        <?php for($i = 0; $i<=3; $i++){  ?>
                        <div class="uk-width-large-1-4">
                            @include('frontend.component.product-item-2')
                        </div>
                        <?php }  ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="uk-container uk-container-center">
            <div class="panel-group">
                <div class="panel-body">
                    <div class="group-title">Stay home & get your daily <br> needs from our shop</div>
                    <div class="group-description">Start Your Daily Shopping with Nest Mart</div>
                    <span class="image img-scaledowm"><img src="resources/img/banner-9-min.png" alt=""></span>
                </div>
            </div>
        </div>
        <div class="panel-commit">
            <div class="uk-container uk-container-center">
                <div class="uk-grid uk-grid-medium">
                    <div class="uk-width-large-1-5">
                        <div class="commit-item">
                            <div class="uk-flex uk-flex-middle">
                                <span class="image"><img src="resources/img/commit-1.png" alt=""></span>
                                <div class="info">
                                    <div class="title">Giá ưu đãi</div>
                                    <div class="description">Khi mua từ 500.000đ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-5">
                        <div class="commit-item">
                            <div class="uk-flex uk-flex-middle">
                                <span class="image"><img src="resources/img/commit-2.png" alt=""></span>
                                <div class="info">
                                    <div class="title">Miễn phí vận chuyển</div>
                                    <div class="description">Trong bán kính 2km</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-5">
                        <div class="commit-item">
                            <div class="uk-flex uk-flex-middle">
                                <span class="image"><img src="resources/img/commit-3.png" alt=""></span>
                                <div class="info">
                                    <div class="title">Ưu đãi</div>
                                    <div class="description">Khi đăng ký tài khoản</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-5">
                        <div class="commit-item">
                            <div class="uk-flex uk-flex-middle">
                                <span class="image"><img src="resources/img/commit-4.png" alt=""></span>
                                <div class="info">
                                    <div class="title">Đa dạng </div>
                                    <div class="description">Sản phẩm đa dạng</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-5">
                        <div class="commit-item">
                            <div class="uk-flex uk-flex-middle">
                                <span class="image"><img src="resources/img/commit-5.png" alt=""></span>
                                <div class="info">
                                    <div class="title">Đổi trả </div>
                                    <div class="description">Đổi trả trong ngày</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('frontend.component.popup')
@endsection