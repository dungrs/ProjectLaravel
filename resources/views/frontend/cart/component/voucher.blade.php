<div class="panel-voucher uk-hidden">
    <div class="voucher-list">
        @for ($j = 0; $j < 3; $j++)
            <div class="voucher-item {{ $j == 0 ? 'active' : '' }}">
                <div class="voucher-left">
                    <!-- Nội dung bên trái -->
                </div>
                <div class="voucher-right">
                    <div class="voucher-title">5AFDSFFD34 <span>(Còn 20)</span></div>
                    <div class="voucher-description">
                        <p>Khuyến mãi nhân dịp Tết 2025, giảm giá đến 25% sản phẩm</p>
                    </div>
                </div>
            </div>
        @endfor
    </div>
    <div class="voucher-form">
        <input type="text" placeholder="Chọn mã giảm giá" name="voucher" value="" readonly>
        <a href="#" class="apply-voucher">Áp dụng</a>
    </div>
</div>