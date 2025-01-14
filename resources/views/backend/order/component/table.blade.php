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
        <th class="text-center">Trạng thái</th>
        <th>Thanh toán</th>
        <th>Giao hàng</th>
        <th class="text-center">Hình thức</th>
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
                        <a href="{{ route('order.detail', ['id' => $order->id]) }}">{{ $order->code }}</a>
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
                    <td class="text-center">
                        {!! $order->confirm != 'cancel' ? __('cart.confirm')[$order->confirm] : '<span class="cancel-badge">'.__('cart.confirm')[$order->confirm] .'</span>' !!}
                    </td>
                    @foreach (__('cart') as $key => $item)
                        @if ($key === 'confirm') @continue @endif
                        <td class="text-center">
                            @if ($order->confirm != 'cancel')
                                <select data-field="{{ $key }}" name="{{ $key }}" class="setupSelect2 updateBadge" id="">
                                    @foreach ($item as $keyOption => $valOption)
                                        @if ($keyOption === 'none') @continue @endif
                                        <option {{ $order->{$key} == $keyOption ? 'selected' : '' }} value="{{ $keyOption }}">{{ $valOption }}</option>
                                    @endforeach
                                </select>
                            @else
                            -                                
                            @endif
                            <input 
                            type="hidden" 
                            class="changeOrderStatus" 
                            value="{{ $order->{$key} }}" 
                            data-title="{{ $item[$order->{$key}] ?? '' }}">
                        </td>
                    @endforeach
                    <td class="text-center">
                        <img class="image img-method" src="{{ array_column(__('payment.method'), 'image', 'name')[$order->method] }}" alt="">
                        <input type="hidden" class="confirm" value="{{ $order->confirm }}">
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $orders-> links('pagination::bootstrap-4') }}