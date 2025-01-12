<!DOCTYPE html>
<html>
<head>
    <title>Mail Đơn Hàng</title>
    <style>
        .cart-success{
            padding: 30px 10px;
        }
        @media (min-width: 1220px){
            .cart-success{
                width:800px;
                margin:0 auto;
            }
        }
        .cart-success .cart-heading{
            text-align: center;
            margin-bottom:30px;
        }
        .cart-success .cart-heading > span{
            text-transform: uppercase;
            font-weight: 700;
        }
        .discover-text > *{
            display: inline-block;
            padding:10px 25px;
            background: #2f5acf;
            border-radius: 16px;
            cursor:pointer;
            color:#fff;
        }
        .discover-text{
            text-align: center;
        }
        .checkout-box{
            border:1px solid #000;
            padding:15px 20px;
            border-radius: 16px;
        }
        .cart-success .panel-body{
            margin-bottom:40px;
        }
        .checkout-box-head{
            margin-bottom:30px;
        }

        .checkout-box-head .order-title{
            border:1px solid #000;
            border-radius: 16px;
            padding:10px 15px;
            font-weight: 700;
            font-size:16px;
        }
        .checkout-box-head .order-date{
            display: flex;
            align-items: center;
            font-size:16px;
            font-weight: bold;
            text-align: center;
        }
        .cart-success .table{
            width:100%;
            border-spacing: 0;
            background: #d9d9d9;
            border-collapse: inherit;
        }
        .cart-success .table thead>tr th{
            color:#fff;
            background: #2f5acf;
            font-weight: 500;
            font-size:14px;
            vertical-align: middle;
            border-bottom: 2px solid #dee2e6;
            text-align: center;
            border:none;
            padding:12px 15px;
        }
        .cart-success tbody tr td{
            padding:12px 15px;
            vertical-align: middle;
            font-size: 14px;
            color:#000;
            border-bottom:1px solid #ccc;
            max-width: 200px !important;
        }
        .cart-success tfoot{
            background: #fff;
        }
        .cart-success tfoot tr td{
            padding:8px;
        }

        .cart-success .table td:last-child{
            text-align: right;
        }
        .cart-success .table tbody tr:nth-child(2n) td {
            background-color: #d9d9d9;
        }
        .total_payment{
            font-weight: bold;
            font-size:24px;
        }
        .panel-foot .checkout-box div{
            margin-bottom:15px;
            font-weight: 500;
        }

        .order-title {
            text-align: center;
        }

        .order-date {
            display: flex;
            text-align: right;
            margin-top: 20px;
            justify-content: end;
        }

        .info-order {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="cart-success">
        <div class="panel-body">
            <h2 class="cart-heading"><span>Thông tin đơn hàng</span></h2>
            <div class="checkout-box">
                <div class="checkout-box-head">
                    <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle">
                        <div class="uk-width-large-1-3"></div>
                        <div class="uk-width-large-1-3">
                            <div class="order-title uk-text-center">ĐƠN HÀNG #{{ $data->first()->code }}</div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="order-date">{{ convertDateTime($data->first()->created_at) }}</div>
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
                            @foreach ($data as $key => $val)
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
                                <td><strong>{{ $data->first()->promotion['code'] }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="4">Tổng giá trị sản phẩm</td>
                                <td><strong>{{ convert_price($data->first()->promotion['discount'] + $data->first()->cart['cartTotal']) }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="4">Tổng giá trị khuyến mãi</td>
                                <td><strong>{{ convert_price($data->first()->promotion['discount']) }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="4">Phí giao hàng</td>
                                <td><strong>0</strong></td>

                            </tr>
                            <tr>
                                <td colspan="4"><span class="total_payment">Tổng thanh toán</span></td>
                                <td><strong>{{ convert_price($data->first()->cart['cartTotal']) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel-foot">
            <h2 class="cart-heading"><span>Thông tin nhận hàng</span></h2>
            <div class="checkout-box">
                <div class="info-order">Tên người nhận: <span>{{ $data->first()->fullname }}</span></div>
                <div class="info-order">Email: <span>{{ $data->first()->email }}</span></div>
                <div class="info-order">Địa chỉ: <span> {{ $data->first()->address }}</span></div>
                <div class="info-order">Số điện thoại: <span>{{ $data->first()->phone }}</span></div>
                <div class="info-order">Hình thức thanh toán: <span>{{ array_column(__('payment.method'), 'title', 'name')[$data->first()->method] }}</span></div>
            </div>
        </div>
    </div>
</body>
</html>