<div class="mb10"><div class="text-danger"><i>* Tổng cuối là tổng chưa bao gồm giảm giá</i></div></div>
<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th class="text-center" style="width: 50px;">
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>Mã</th>
        <th>Ngày tạo</th>
        <th>Khách hàng</th>
        <th>Giảm giá</th>
        <th>Phí ship</th>
        <th>Tổng cuối</th>
        <th>Giao hàng</th>
        <th>Trạng thái</th>
        <th>Thanh toán</th>
        <th>Hình thức</th>
    </tr>
    </thead>
    <tbody>
        @if(isset($orders) && is_object($orders))
            @foreach ($orders as $order)
                <tr id="{{ $order->id }}">
                    <td class="text-center">
                        <input type="checkbox" value="{{ $order->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <a href="">{{ $order->code }}</a>
                    </td>
                    <td>
                        {{ convertDateTime($order->created_at) }}
                    </td>
                    <td>
                        <div><b>N: </b>{{ $order->fullname }}</div>
                        <div><b>P: </b>{{ $order->phone }}</div>
                        <div><b>A: </b>{{ $order->address }}</div>
                    </td>
                    <td class="text-danger font-bold">
                        {{ convert_price($order->promotion['discount']) }}
                    </td>
                    <td class="text-success font-bold">
                        {{ convert_price($order->shipping) }}
                    </td>
                    <td class="text-success font-bold">
                        {{ convert_price($order->cart['cartTotal']) }}
                    </td>
                    <td>
                        {{ __('cart.delivery')[$order->delivery] }}
                    </td>
                    <td>
                        {{ __('cart.confirm')[$order->confirm] }}
                    </td>
                    <td>
                        {{ __('cart.payment')[$order->payment] }}
                    </td>
                    <td>
                        {{ array_column(__('payment.method'), 'title', 'name')[$order->method] }}
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $orders-> links('pagination::bootstrap-4') }}