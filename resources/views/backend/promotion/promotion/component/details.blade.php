<div class="ibox">
    <div class="ibox-title">
        <h5>Cài đặt thông tin chi tiết khuyến mãi</h5>
    </div>
    @php
        $promotionMethod = old("method", $promotion->method ?? null);
    @endphp
    <div class="ibox-content">
        <div class="form-row">
            <div class="fix-label">Chọn hình thức khuyến mãi</div>
            <select name="method" class="setupSelect2 promotionMethod" id="">
                <option value="">Chọn hình thức</option>
                @foreach (__('module.promotion') as $key => $val)
                    <option 
                        value="{{ $key }}"
                        {{ $promotionMethod === $key ? 'selected' : '' }} 
                    >{{ $val }}</option>
                @endforeach
            </select>
        </div>
        <div class="promotion-container">
            <!-- Nội dung sẽ được thêm vào ở đây (ví dụ: theo lựa chọn trong dropdown) -->
        </div>
    </div>
</div>