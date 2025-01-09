@extends('frontend.homepage.layout')
@section('content')
    <div class="cart-container">
        <div class="uk-container uk-container-center">
            <form action="uk-form form" class="uk-form form" method="post">
                @csrf
                <div class="cart-wrapper">
                    <div class="uk-grid uk-grid-medium">
                        <div class="uk-width-large-3-5">
                            <div class="panel-cart cart-left">
                                <div class="panel-head">
                                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                        <h2 class="cart-heading">
                                            <span>Thông tin đặt hàng</span>
                                        </h2>
                                        <span class="has-account">Bạn đã có tài khoản? <a href="Đăng nhập ngay">Đăng nhập ngay</a></span>
                                    </div>
                                </div>
                                <div class="panel-body mb30">
                                    <div class="cart-information">
                                        <div class="uk-grid uk-grid-medium mb20">
                                            <div class="uk-width-large-1-2">
                                                <div class="form-row">
                                                    <input type="text" name="fullname" value="" placeholder="Nhập vào Họ Tên" class="input-text">
                                                </div>
                                            </div>
                                            <div class="uk-width-large-1-2">
                                                <div class="form-row">
                                                    <input type="text" name="phone" value="" placeholder="Nhập vào Số điện thoại" class="input-text">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row mb20">
                                            <input type="text" name="email" value="" placeholder="Nhập vào Email" class="input-text">
                                        </div>
                                        <div class="uk-grid uk-grid-medium mb20">
                                            <div class="uk-width-large-1-3">
                                                <select name="province_id" class="form-control nice-select province location" data-target="districts" id="">
                                                    <option value="0">[Chọn Thành Phố]</option>
                                                    @if (isset($provinces))
                                                        @foreach ($provinces as $province)
                                                            <option @if (old('province_id') == $province->code)
                                                                selected
                                                            @endif 
                                                            value="{{ $province->code }}">{{ $province->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="uk-width-large-1-3">
                                                <select name="district_id" class="form-control nice-select districts location" data-target="wards" id="">
                                                    <option value="0">[Chọn Quận\Huyện]</option>
                                                </select>
                                            </div>
                                            <div class="uk-width-large-1-3">
                                                <select name="ward_id" class="form-control nice-select wards" id="">
                                                    <option value="0">[Chọn Phường/Xã]</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row mb20">
                                            <input type="text" name="address" value="" placeholder="Nhập vào Địa chỉ" class="input-text">
                                        </div>
                                        <div class="form-row">
                                            <input type="text" name="description" value="" placeholder="Ghi chú thêm (Ví dụ: Giao hàng vào lúc 3 giờ chiều)" class="input-text">
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <h2 class="cart-heading"><span>Hình Thức Thanh Toán</span></h2>
                                    <div class="cart-method mb30">
                                        @foreach (__('payment.method') as $key => $val)
                                            <label for="{{ $val['name'] }}" class="uk-flex uk-flex-middle method-item">
                                                <input type="radio" name="method" value="{{ $val['name'] }}" {{ $key = 0 ? 'checked' : '' }} id="{{ $val['name'] }}">
                                                <span class=image><img src="{{ $val['image'] }}" alt=""></span>
                                                <span class="title">{{ $val['title'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="cart-return mb10">
                                        <span>{!! __('payment.return') !!}</span>
                                    </div>
                                    <button type="submit" class="cart-checkout" value="create" name="create">Thanh toán đơn hàng</button>
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-large-2-5">
                            <div class="panel-cart">
                                <div class="panel-head">
                                    <h2 class="cart-heading"><span>Giỏ hàng</span></h2>
                                </div>
                                <div class="panel-body">
                                    <div class="cart-list">
                                        @php
                                            $total = 0
                                        @endphp
                                        @foreach ($carts as $cart)
                                            @php
                                                $total += $cart->price * $cart->qty
                                            @endphp
                                        @endforeach
                                        @if (count($carts) && !is_null($carts))
                                            @foreach ($carts as $cart)
                                                <div class="cart-item">
                                                    <div class="uk-grid uk-grid-medium">
                                                        <div class="uk-width-small-1-1 uk-width-medium-1-5">
                                                            <div class="cart-item-image">
                                                                <span class="image img-scaledown">
                                                                    <img src="{{ $cart->image }}" alt="">
                                                                </span>
                                                                <span class="cart-item-number">{{ $cart->qty }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="uk-width-small-1-1 uk-width-medium-4-5">
                                                            <div class="cart-item-info">
                                                                <h3 class="title"><span>{{ $cart->name }}</span></h3>
                                                                <div class="cart-item-action uk-flex uk-flex-middle uk-flex-space-between">
                                                                    <div class="cart-item-qty">
                                                                        <button type="button" class="btn-qty minus">-</button>
                                                                        <input type="text" class="input-qty" value="{{ $cart->qty }}">
                                                                        <button type="button" class="btn-qty plus">+</button>
                                                                    </div>
                                                                    <div class="cart-item-price">
                                                                        <div class="uk-flex uk-flex-middle">
                                                                            @if ($cart->price != $cart->priceOriginal)
                                                                                <span class="cart-price-old mr10">{{ convert_price($cart->priceOriginal * $cart->qty) }}</span>
                                                                            @endif
                                                                            <span class="cart-price-sale">{{ convert_price($cart->price * $cart->qty) }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cart-item-remove">
                                                                        <span>X</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="panel-voucher uk-hidden">
                                    <div class="voucher-list">
                                        @for ($j = 0; $j < 3; $j++)
                                            <div class="voucher-item {{ $j == 0 ? 'active' : '' }}">
                                                <div class="voucher-left">
                                                    <!-- Nội dung bên trái -->
                                                </div>
                                                <div class="voucher-right">
                                                    <div class="voucher-title">5AFDSFFD34 <span>(Còn 20)</span></div>
                                                    <div class="voucher-description">
                                                        <p>Khuyến mãi nhân dịp Tết 2025, giảm giá đến 25% sản phẩm</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                    <div class="voucher-form">
                                        <input type="text" placeholder="Chọn mã giảm giá" name="voucher" value="" readonly>
                                        <a href="#" class="apply-voucher">Áp dụng</a>
                                    </div>
                                </div>
                                <div class="panel-foot mt30">
                                    <div class="cart-summary">
                                        <div class="cart-summary-item">
                                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                                <span class="summary-title">Giảm giá</span>
                                                <div class="summary-value">-0đ</div>
                                            </div>
                                        </div>
                                        <div class="cart-summary-item">
                                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                                <span class="summary-title">Phí giao hàng</span>
                                                <div class="summary-value">Miễn phí</div>
                                            </div>
                                        </div>
                                        <div class="cart-summary-item">
                                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                                <span class="summary-title bold">Tổng tiền</span>
                                                <div class="summary-value cart-total">{{ convert_price($total) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- <script>
        var province_id = '{{ (isset($user->province_id)) ? $user->province_id : old('province_id') }}';
        var district_id = '{{ (isset($user->district_id)) ? $user->district_id : old('district_id') }}';
        var ward_id = '{{ (isset($user->ward_id)) ? $user->ward_id : old('ward_id') }}';
    </script> --}}
@endsection