@extends('frontend.homepage.layout')
@section('content')
    <div class="cart-success">
        <div class="panel-head">
            <h2 class="cart-heading"><span>Đặt hàng thành công</span></h2>
            <div class="discover-text"><a href="{{ writeUrl('san-pham') }}">Khám phá thêm các sản phẩm tại đây</a></div>
        </div>
        <div class="panel-body">
            <h2 class="cart-heading"><span>Thông tin đơn hàng</span></h2>
            <div class="checkout-box">
                <div class="checkout-box-head">
                    <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle">
                        <div class="uk-width-large-1-3"></div>
                        <div class="uk-width-large-1-3">
                            <div class="order-title uk-text-center">ĐƠN HÀNG #{{ $data['order']->first()->code }}</div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="order-date">{{ convertDateTime($data['order']->first()->created_at) }}</div>
                        </div>
                    </div>
                </div>
                <div class="checkout-box-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="uk-text-left">Tên sản phẩm</th>
                                <th class="uk-text-center">Số lượng</th>
                                <th class="uk-text-right">Giá niêm yết</th>
                                <th class="uk-text-right">Giá bán</th>
                                <th class="uk-text-right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['order'] as $key => $val)
                                <tr>
                                    <td class="uk-text-left">{{ $val->name }}</td>
                                    <td class="uk-text-center">{{ $val->qty }}</td>
                                    <td class="uk-text-right">{{ convert_price($val->price_original) }}</td>
                                    <td class="uk-text-right">{{ convert_price($val->price) }}</td>
                                    <td class="uk-text-right">{{ convert_price($val->price * $val->qty) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">Mã giảm giá</td>
                                <td><strong>{{ $data['order']->first()->promotion['code'] }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="4">Tổng giá trị sản phẩm</td>
                                <td><strong>{{  convert_price($data['order']->first()->cart['cartTotal']) }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="4">Tổng giá trị khuyến mãi</td>
                                <td><strong>{{ convert_price($data['order']->first()->promotion['discount']) }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="4">Phí giao hàng</td>
                                <td><strong>0</strong></td>

                            </tr>
                            <tr>
                                <td colspan="4"><span class="total_payment">Tổng thanh toán</span></td>
                                <td><strong>{{ convert_price($data['order']->first()->cart['cartTotal'] - $data['order']->first()->promotion['discount']) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel-foot">
            <h2 class="cart-heading"><span>Thông tin nhận hàng</span></h2>
            <div class="checkout-box">
                <div class="uk-flex uk-flex-space-between">Tên người nhận: <span>{{ $data['order']->first()->fullname }}</span></div>
                <div class="uk-flex uk-flex-space-between">Email: <span>{{ $data['order']->first()->email }}</span></div>
                <div class="uk-flex uk-flex-space-between">Địa chỉ: <span> {{ $data['order']->first()->address }}</span></div>
                <div class="uk-flex uk-flex-space-between">Số điện thoại: <span>{{ $data['order']->first()->phone }}</span></div>
                <div class="uk-flex uk-flex-space-between">Hình thức thanh toán: <span>{{ array_column(__('payment.method'), 'title', 'name')[$data['order']->first()->method] }}</span></div>

                @include($data['template'] ?? '')
            </div>
        </div>
    </div>
@endsection