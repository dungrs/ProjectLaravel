@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['detail']['title']])
<div class="order-wrapper">
    <div class="row">
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-title-left">
                        <span>Chi tiết đơn hàng</span>
                        <spac class="badge">
                            <div class="badge__tip"></div>
                            <div class="badge-text">Chưa giao</div>
                        </spac>
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
                <div class="payment-confirm">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <div class="uk-flex uk-flex-middle">
                            <span class="icon"><img src="{{ asset('backend/img/warning.png') }}" alt=""></span>
                            <div class="payment-title">
                                <div class="text_1">
                                    <span class="isConfirm">ĐANG CHỜ XÁC NHẬN ĐƠN HÀNG</span>
                                    20.000.000 đ
                                </div>
                                <div class="text_2">
                                    Thanh toán khi nhận được hàng (COD)
                                </div>
                            </div>
                        </div>
                        <div class="cancle-block">
                            {{-- <button class="button">Hủy đơn</button> --}}
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
                        <div class="cancle-block">
                            <button class="button confirm">Xác nhận</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 order-aside">
            <div class="ibox">
                <div class="ibox-title">
                    <span>Ghi chú</span>
                    <div class="edit span">Sửa</div>
                </div>
                <div class="ibox-content">
                    <div class="description">
                        {{ $order->first()->description }}
                    </div>
                </div>
            </div>
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Thông tin khách hàng</h5>
                    <div class="edit span">Sửa</div>
                </div>
                <div class="ibox-content">
                    <div class="custom-line">
                        <strong>N:</strong> {{ $order->first()->fullname }}
                    </div>
                    <div class="custom-line">
                        <strong>E:</strong> {{ $order->first()->email }}
                    </div>
                    <div class="custom-line">
                        <strong>P:</strong> {{ $order->first()->phone }}
                    </div>
                    <div class="custom-line">
                        <strong>A:</strong> {{ $order->first()->address }}
                    </div>
                    <div class="custom-line">
                        <strong>P:</strong> {{ $order->first()->ward_name }}
                    </div>
                    <div class="custom-line">
                        <strong>Q:</strong> {{ $order->first()->district_name }}
                    </div>
                    <div class="custom-line">
                        <strong>T:</strong> {{ $order->first()->province_name }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>