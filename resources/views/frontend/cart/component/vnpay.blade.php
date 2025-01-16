<div class="table-responsive">
    <div style="display: flex; justify-content: space-between;">
        <label>Mã đơn hàng:</label>
        <label>{{ $_GET['vnp_TxnRef'] ?? 'Không có dữ liệu' }}</label>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <label>Số tiền:</label>
        <label>{{  convert_price($_GET['vnp_Amount']) ?? 'Không có dữ liệu' }} VND</label>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <label>Nội dung thanh toán:</label>
        <label>{{ $_GET['vnp_OrderInfo'] ?? 'Không có dữ liệu' }}</label>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <label>Mã phản hồi (vnp_ResponseCode):</label>
        <label>{{ $_GET['vnp_ResponseCode'] ?? 'Không có dữ liệu' }}</label>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <label>Mã GD tại VNPAY:</label>
        <label>{{ $_GET['vnp_TransactionNo'] ?? 'Không có dữ liệu' }}</label>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <label>Mã Ngân hàng:</label>
        <label>{{ $_GET['vnp_BankCode'] ?? 'Không có dữ liệu' }}</label>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <label>Thời gian thanh toán:</label>
        <label>{{ date('d/m/Y H:i:s', strtotime($_GET['vnp_PayDate'] ?? '')) ?? 'Không có dữ liệu' }}</label>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <label>Trạng thái giao dịch:</label>
        <label>
            {{ $_GET['vnp_ResponseCode'] == '00' ? 'Thành công' : 'Thất bại' }}
        </label>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <label>Kết quả:</label>
        <label>
            @if ($data['secureHash'] === $data['vnp_SecureHash'])
                @if (request()->get('vnp_ResponseCode') === '00')
                    <span style="color: blue">Giao dịch qua công VNPAY thành công</span>
                @else
                    <span style="color: red">Giao dịch qua cổng VNPAY không thành công</span>
                @endif
            @else
                <span style="color: red">Chữ ký không hợp lệ</span>
            @endif
        </label>
    </div>
    
</div>