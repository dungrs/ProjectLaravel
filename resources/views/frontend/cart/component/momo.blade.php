@php
    $system
@endphp

@if ($data['m2signature'] == $data['partnerSignature'])
    @if ($data['resultCode'] == '0')
        <div style="display: flex; justify-content: space-between;">
            <label>Tình trạng thanh toán:</label>
            <label>Thành công</label>
        </div>
    @else
        <div style="display: flex; justify-content: space-between;">
            <label>Tình trạng thanh toán:</label>
            <label>Thất bại</label>
        </div>
    @endif
@else
    <div class="alert alert-danger">
        Giao dịch thanh toán không thành công!
    </div>
@endif
