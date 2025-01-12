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
                            <div class="order-title uk-text-center">ĐƠN HÀNG #{{ $order->first()->code }}</div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="order-date">{{ convertDateTime($order->first()->created_at) }}</div>
                        </div>
                    </div>
                </div>
                <div class="checkout-box-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Giá niêm yết</th>
                                <th>Giá bán</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order as $key => $val)
                                <tr>
                                    <td>{{ $val->name }}</td>
                                    <td>{{ $val->qty }}</td>
                                    <td>{{ convert_price($val->price_original) }}</td>
                                    <td>{{ convert_price($val->price) }}</td>
                                    <td>{{ convert_price($val->price * $val->qty) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">Mã giảm giá</td>
                                <td> {{ $order->first()->promotion['code'] }}</td>
                            </tr>
                            <tr>
                                <td colspan="4">Tổng giá trị sản phẩm</td>
                                <td>{{ convert_price($order->first()->promotion['discount'] + $order->first()->cart['cartTotal']) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4">Tổng giá trị khuyến mãi</td>
                                <td>{{ convert_price($order->first()->promotion['discount']) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4">Phí giao hàng</td>
                                <td>0</td>

                            </tr>
                            <tr>
                                <td colspan="4"><span class="total_payment">Tổng thanh toán</span></td>
                                <td>{{ convert_price($order->first()->cart['cartTotal']) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel-foot">
            <h2 class="cart-heading"><span>Thông tin nhận hàng</span></h2>
            <div class="checkout-box">
                <div class="uk-flex uk-flex-space-between">Tên người nhận: <span>{{ $order->first()->fullname }}</span></div>
                <div class="uk-flex uk-flex-space-between">Email: <span>{{ $order->first()->email }}</span></div>
                <div class="uk-flex uk-flex-space-between">Địa chỉ: <span> {{ $order->first()->address }}</span></div>
                <div class="uk-flex uk-flex-space-between">Số điện thoại: <span>{{ $order->first()->phone }}</span></div>
                <div class="uk-flex uk-flex-space-between">Hình thức thanh toán: <span>{{ array_column(__('payment.method'), 'title', 'name')[$order->first()->method] }}</span></div>
            </div>
        </div>
    </div>
@endsection