@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['detail']['title']])
<div class="order-wrapper">
    <div class="row">
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-title-left">
                        <span>Chi tiết đơn hàng #{{ $order->first()->code }}</span>
                        <span class="badge">
                            <div class="badge__tip"></div>
                            <div class="badge-text">{{ __('cart.delivery')[$order->first()->delivery] }}</div>
                        </span>
                        <span class="badge">
                            <div class="badge__tip"></div>
                            <div class="badge-text">{{ __('cart.payment')[$order->first()->payment] }}</div>
                        </span>
                    </div>
                    <div class="ibox-title-right">
                        Nguồn: Website
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table-order">
                        <tbody>
                            @foreach ($order as $item)
                                <tr class="order-item">
                                    <td class="image">
                                        <span class="iamge img-scaledown">
                                            <img src="{{ $item->album }}" alt="">
                                        </span>
                                    </td>
                                    <td>
                                        <div class="order-item-name">{{ $item->name }}</div>
                                        <div class="order-tiem-voucher">Mã giảm giá: -</div>
                                    </td>
                                    <td>
                                        <div class="order-item-price">{{ convert_price($item->price) }}</div>
                                    </td>
                                    <td>
                                        <div class="order-item-times">x</div>
                                    </td>
                                    <td>
                                        <div class="order-item-qty">{{ $item->qty }}</div>
                                    </td>
                                    <td>
                                        <div class="order-item-subtotal">
                                            {{ convert_price($item->price * $item->qty) }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="5" class="text-right">Tổng tạm</td>
                                <td class="text-right">{{ convert_price($order->first()->cart['cartTotal']) }}</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right">Giảm giá</td>
                                <td class="text-right">{{ convert_price($order->first()->promotion['discount']) }}</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right">Vận chuyển</td>
                                <td class="text-right">0đ</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right"><strong>Tổng cuối</strong></td>
                                <td class="text-right"><strong>{{ convert_price($order->first()->cart['cartTotal'] - $order->first()->promotion['discount']) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="payment-confirm confirm-box">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <div class="uk-flex uk-flex-middle">
                            <span class="icon"><img src="{{ __('order.confirm') == 'pending' ? asset('backend/img/warning.png') : asset('backend/img/correct.png') }}" alt=""></span>
                            <div class="payment-title">
                                <div class="text_1">
                                    <span class="isConfirm">{{ __('order.confirm')[$order->first()->confirm] }}</span>
                                    {{ convert_price($order->first()->cart['cartTotal'] - $order->first()->promotion['discount']) }}
                                </div>
                                <div class="text_2">
                                    {{ array_column(__('payment.method'), 'title', 'name')[$order->first()->method] }}
                            </div>
                            </div>
                        </div>
                        <div class="cancle-block">
                            {!! $order->first()->confirm === 'cancel' ? '<span class="nofiCancel">Đơn hàng đã bị hủy</span>' : '' !!}
                        </div>
                    </div>
                </div>
                <div class="payment-confirm">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <div class="uk-flex uk-flex-middle">
                            <span class="icon"><i class="fa fa-truck"></i></span>
                            <div class="payment-title">
                                <div class="text_1">
                                    Xác nhận đơn hàng
                                </div>
                            </div>
                        </div>
                        <div class="confirm-block">
                            @if ( $order->first()->confirm === 'pending')
                                <button class="button confirm updateField" data-field="confirm" data-confirm="confirm" data-title="ĐÃ XÁC NHẬN ĐƠN HÀNG TRỊ GIÁ">Xác nhận</button>
                            @else
                                <span class="nofiConfirm">Đã xác nhận</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 order-aside">
            <div class="ibox" data-type="orderTarget">
                <div class="ibox-title">
                    <span>Ghi chú</span>
                    <div class="edit span edit-order" data-target="description">Sửa</div>
                </div>
                <div class="ibox-content order-description">
                    <div class="description">
                        {{ $order->first()->description }}
                    </div>
                </div>
            </div>
            <div class="ibox" data-type="orderTarget">
                <div class="ibox-title">
                    <h5>Thông tin khách hàng</h5>
                    <div class="edit span edit-order" data-target="customerInfo">Sửa</div>
                </div>
                <div class="ibox-content order-customer-info">
                    <div class="custom-line">
                        <strong>N: </strong>
                        <span class="fullname">{{ $order->first()->fullname }}</span>
                    </div>
                    <div class="custom-line">
                        <strong>E: </strong>
                        <span class="email">{{ $order->first()->email }}</span>
                    </div>
                    <div class="custom-line">
                        <strong>P: </strong>
                        <span class="phone">{{ $order->first()->phone }}</span>
                    </div>
                    <div class="custom-line">
                        <strong>A: </strong>
                        <span class="address">{{ $order->first()->address }}</span>
                    </div>
                    <div class="custom-line">
                        <strong>P: </strong>
                        <span class="ward_name">{{ $order->first()->ward_name }}</span>
                    </div>
                    <div class="custom-line">
                        <strong>Q: </strong>
                        <span class="district_name">{{ $order->first()->district_name }}</span>
                    </div>
                    <div class="custom-line">
                        <strong>T: </strong>
                        <span class="province_name">{{ $order->first()->province_name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="order_id" value="{{ $order->first()->id }}">
<input type="hidden" name="province_id" value="{{ $order->first()->province_id }}">
<input type="hidden" name="district_id" value="{{ $order->first()->district_id }}">
<input type="hidden" name="ward_id" value="{{ $order->first()->ward_id }}">

<script>
    var provinces = @json($provinces->map(function($item) {
        return [
            'id' => $item->code,
            'name' => $item->name
        ];
    })->values());
</script>