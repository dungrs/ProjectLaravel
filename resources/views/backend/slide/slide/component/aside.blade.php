<div class="col-lg-3">
    <div class="ibox slide-setting slide-normal">
        <div class="ibox-title">
            <h5 style="padding: 9px 10px !important">Cài đặt cơ bản</h5>
        </div>
        <div class="ibox-content">
            <div class="row mb15">
                <div class="col-lg-12 mb15">
                    <div class="form-row">
                        <label for="" class="control-label text-left">Tên Slide
                            <span class="text-danger">(*)</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', ($slide->name ?? '')) }}" class="form-control" placeholder="" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">Từ khóa
                            <span class="text-danger">(*)</span>
                        </label>
                        <input type="text" name="keyword" value="{{ old('keyword', ($slide->keyword ?? '')) }}" class="form-control" placeholder="" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="slide-setting">
                        <div class="setting-item">
                            <div class="uk-flex uk-flex-middle">
                                <span class="setting-text">Chiều rộng</span>
                                <div class="setting-value">
                                    <input type="text" name="setting[width]" value="{{ old('setting.width', ($slide->setting['width']) ?? null) }}" class="form-control int">
                                    <span class="px">px</span>
                                </div>
                            </div>
                        </div>
                        <div class="setting-item">
                            <div class="uk-flex uk-flex-middle">
                                <span class="setting-text">Chiều cao</span>
                                <div class="setting-value">
                                    <input type="text" name="setting[height]" value="{{ old('setting.height', ($slide->setting['height']) ?? null) }}" class="form-control int">
                                    <span class="px">px</span>
                                </div>
                            </div>
                        </div>
                        <div class="setting-item">
                            <div class="uk-flex uk-flex-middle">
                                <span class="setting-text">Hiệu ứng</span>
                                <div class="setting-value">
                                    <select name="setting[animation]" id="" class="form-control setupSelect2">
                                        @foreach (__('module.effect') as $key => $val)
                                            <option {{ $key == old('setting.animation', $slide->setting['animation'] ?? null) ? 'selected' : ''}} value="{{ $key }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="setting-item">
                            <div class="uk-flex uk-flex-middle">
                                <span class="setting-text">Mũi tên</span>
                                <div class="setting-value">
                                    <input type="checkbox" name="setting[arrow]" value="accept" 
                                        @if (!old() || old('setting.arrow', $slide->setting['arrow'] ?? null) == 'accept')
                                            checked="checked"
                                        @endif
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="setting-item">
                            <div class="uk-flex uk-flex-middle">
                                <span class="setting-text">Điều hướng</span>
                                <div class="setting-value">
                                    @foreach (__('module.navigate') as $key => $val)
                                        <div class="nav-setting-item uk-flex uk-flex-middle">
                                            <input id="navigate_{{ $key }}" type="radio" value="{{ $key }}" name="setting[navigate]" {{ old('setting.navigate', $slide->setting['navigate'] ?? 'dots') == $key ? 'checked' : '' }}>
                                            <label for="navigate_{{ $key }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ibox slide-setting slide-advance">
        <div class="ibox-title">
            <div class="ibox-title uk-flex uk-flex-middle uk-flex-space-between" style="padding-top: 0px; border-color: #fff;">
                <h5>Cài đặt nâng cao</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="ibox-content">
            <div class="setting-item">
                <div class="uk-flex uk-flex-middle">
                    <span class="setting-text">Tự động chạy</span>
                    <div class="setting-value">
                        <input type="checkbox" name="setting[autoplay]" value="accept"
                            @if (!old() || old('setting.autoplay', $slide->setting['autoplay'] ?? null) == 'accept')
                                checked="checked"
                            @endif
                        >
                    </div>
                </div>
            </div>
            <div class="setting-item">
                <div class="uk-flex uk-flex-middle">
                    <span class="setting-text">Dừng khi <br/> di chuột</span>
                    <div class="setting-value">
                        <input type="checkbox" name="setting[pauseHover]" 
                        @if (!old() || old('setting.pauseHover', $slide->setting['pauseHover'] ?? null) == 'accept')
                            checked="checked"
                        @endif value="">
                    </div>
                </div>
            </div>
            <div class="setting-item">
                <div class="uk-flex uk-flex-middle">
                    <span class="setting-text">Chuyển ảnh</span>
                    <div class="setting-value">
                        <input type="text" class="form-control int" name="setting[animationDelay]" value="{{ old('setting.animationDelay', $slide->setting['animationDelay'] ?? null) }}">
                        <span class="px">ms</span>
                    </div>
                </div>
            </div>
            <div class="setting-item">
                <div class="uk-flex uk-flex-middle">
                    <span class="setting-text">Tốc độ <br/> hiệu ứng </span>
                    <div class="setting-value">
                        <input type="text" class="form-control int" name="setting[animationSpeed]" value="{{ old('setting.animationSpeed', $slide->setting['animationSpeed'] ?? null) }}">
                        <span class="px">ms</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ibox short-code">
        <div class="ibox-title">
            <h5 style="padding-top: 10px; padding-left: 10px;">Short code</h5>
        </div>
        <div class="ibox-content">
            <textarea name="short_code" class="textarea form-control" id="" cols="30" rows="2">{{ old('short_code', $slide->short_code ?? null) }}</textarea>
        </div>
    </div>
</div>