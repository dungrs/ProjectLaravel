<div class="panel-head">
    <div class="uk-flex uk-flex-middle uk-flex-space-between">
        <h2 class="cart-heading">
            <span>Thông tin đặt hàng</span>
        </h2>
        <span class="has-account">Bạn đã có tài khoản? <a href="Đăng nhập ngay">Đăng nhập ngay</a></span>
    </div>
</div>
<div class="panel-body mb30">
    <div class="cart-information">
        <div class="uk-grid uk-grid-medium mb20">
            <div class="uk-width-large-1-2">
                <div class="form-row">
                    <input type="text" name="fullname" value="{{ old('fullname') }}" placeholder="Nhập vào Họ Tên" class="input-text">
                </div>
            </div>
            <div class="uk-width-large-1-2">
                <div class="form-row">
                    <input type="text" name="phone" value="{{ old('name') }}" placeholder="Nhập vào Số điện thoại" class="input-text">
                </div>
            </div>
        </div>
        <div class="form-row mb20">
            <input type="text" name="email" value="{{ old('email') }}" placeholder="Nhập vào Email" class="input-text">
        </div>
        <div class="uk-grid uk-grid-medium mb20">
            <div class="uk-width-large-1-3">
                <select name="province_id" class="form-control setupSelect2 province location" data-target="districts" id="">
                    <option value="0">[Chọn Thành Phố]</option>
                    @if (isset($provinces))
                        @foreach ($provinces as $province)
                            <option @if (old('province_id') == $province->code)
                                selected
                            @endif 
                            value="{{ $province->code }}">{{ $province->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="uk-width-large-1-3">
                <select name="district_id" class="form-control setupSelect2 districts location" data-target="wards" id="">
                    <option value="0">[Chọn Quận\Huyện]</option>
                </select>
            </div>
            <div class="uk-width-large-1-3">
                <select name="ward_id" class="form-control setupSelect2 wards" id="">
                    <option value="0">[Chọn Phường/Xã]</option>
                </select>
            </div>
        </div>
        <div class="form-row mb20">
            <input type="text" name="address" value="{{ old('address') }}" placeholder="Nhập vào Địa chỉ: ví dụ đường Lạc Long Quân" class="input-text">
        </div>
        <div class="form-row">
            <input type="text" name="description" value="{{ old('description') }}" placeholder="Ghi chú thêm (Ví dụ: Giao hàng vào lúc 3 giờ chiều)" class="input-text">
        </div>
    </div>
</div>