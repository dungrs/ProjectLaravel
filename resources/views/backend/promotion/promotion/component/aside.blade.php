<div class="col-lg-3">
    <div class="ibox slide-setting slide-normal">
        <div class="ibox-title">
            <h5 style="padding: 9px 10px !important">Cài đặt cơ bản</h5>
        </div>
        <div class="ibox-content">
            <div class="row mb15">
                <div class="col-lg-12 mb15">
                    <div class="form-row">
                        <label for="" class="control-label text-left">Tên Widget
                            <span class="text-danger">(*)</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', ($widget->name ?? '')) }}" class="form-control" placeholder="" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">Từ khóa Widget
                            <span class="text-danger">(*)</span>
                        </label>
                        <input type="text" name="keyword" value="{{ old('keyword', ($widget->keyword ?? '')) }}" class="form-control" placeholder="" autocomplete="off">
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
            <textarea name="short_code" class="textarea form-control" id="" cols="30" rows="2">{{ old('short_code', $widget->short_code ?? null) }}</textarea>
        </div>
    </div>
</div>