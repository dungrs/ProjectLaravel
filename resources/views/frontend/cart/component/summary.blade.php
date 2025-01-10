<div class="panel-foot mt30">
    <div class="cart-summary">
        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summary-title">Giảm giá</span>
                <div class="summary-value discount-value">-{{ convert_price($cartPromotion['discount']) }}</div>
            </div>
        </div>
        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summary-title">Phí giao hàng</span>
                <div class="summary-value">Miễn phí</div>
            </div>
        </div>
        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summary-title bold">Tổng tiền</span>
                <div class="summary-value cart-total">{{ convert_price($reCalculateCart['cartTotal'] - $cartPromotion['discount']) }}</div>
            </div>
        </div>
    </div>
</div>