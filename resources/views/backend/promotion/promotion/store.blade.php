@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('widget.store') : route("widget.update", $widget->id)
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight promotion-wrapper">
        <div class="row">
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin chung</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên chương trình <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $promotion->name ?? '') }}" placeholder="Nhập vào mã khuyến mại" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mã khuyến mại <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="code" value="{{ old('code', $promotion->code ?? '') }}" placeholder="Nhập vào tên khuyến mại" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="control-label text-left">Mô tả khuyến mại</label>
                                    <textarea name="description" class="form-control form-textarea" style="height: 100px">{{ old('description', $promotion->description ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Cài đặt thông tin chi tiết khuyến mãi</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-row">
                            <div class="fix-label font-bold">Chọn hình thức khuyến mãi</div>
                            <select name="" class="setupSelect2" id="">
                                <option value="">Chọn hình thức</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thời gian áp dụng chương trình</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-row mb15">
                            <label for="" class="control-label text-left">Ngày bắt đầu <span class="text-danger">(*)</span></label>
                            <div class="form-date">
                                <input type="text" class="form-control" name="start_date" value="{{ old('start_date', $promotion->start_date ?? '') }}" placeholder="" autocomplete="off">
                                <span><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="form-row mb15">
                            <label for="" class="control-label text-left">Ngày kết thúc <span class="text-danger">(*)</span></label>
                            <div class="form-date">
                                <input type="text" class="form-control" name="end_date" value="{{ old('end_date', $promotion->end_date ?? '') }}" placeholder="" autocomplete="off">
                                <span><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="uk-flex uk-flex-middle">
                                <input type="checkbox" name="" value="accept" class="" id="neverEnd">
                                <label for="neverEnd" class="fix-label ml5">Không có ngày kết thúc</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Nguồn khách áp dụng</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="uk-flex uk-flex-middle">
                            <div class="setting-value">
                                <div class="nav-setting-item uk-flex uk-flex-middle">
                                    <input id="navigate_hide" type="radio" value="hide" name="source">
                                    <label class="fix-label ml5" for="navigate_hide">Áp dụng cho toàn bộ nguồn khách</label>
                                </div>
                                <div class="nav-setting-item uk-flex uk-flex-middle">
                                    <input id="navigate_dots" type="radio" value="dots" name="source" checked="">
                                    <label class="fix-label ml5" for="navigate_dots">Chọn nguồn khách áp dụng</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Đối tượng áp dụng</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="uk-flex uk-flex-middle">
                            <div class="setting-value">
                                <div class="nav-setting-item uk-flex uk-flex-middle">
                                    <input id="navigate_hide" type="radio" value="hide" name="source">
                                    <label class="fix-label ml5" for="navigate_hide">Áp dụng cho toàn bộ khách hàng</label>
                                </div>
                                <div class="nav-setting-item uk-flex uk-flex-middle">
                                    <input id="navigate_dots" type="radio" value="dots" name="source" checked="">
                                    <label class="fix-label ml5" for="navigate_dots">Chọn khách hàng áp dụng</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-right mb15">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>

<script>
    const checkIconPath = "{{ asset('backend/img/check.png') }}";
    const deleteIconPath = "{{ asset('backend/img/close.png') }}"
</script>