@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('generate.store') : route("generate.update", $generate->id)
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>- Thông tin chung của nhóm ngôn ngữ</p>
                        <p>- Lưu ý: Những trường đánh dấu <span class="text-danger">(*)</span>
                        là bắt buộc</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin chung</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên Model
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text" name="name" value="{{ old('name', ($generate->name ?? '')) }}" class="form-control" placeholder="" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên chức năng
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text" name="module" value="{{ old('module', ($generate->module ?? '')) }}" class="form-control" placeholder="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Loại Module
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <select name="module_type" id="" class="setupSelect2 form-control">
                                        <option value="0">Chọn loại Module</option>
                                        <option value="catalogue">Module danh mục</option>
                                        <option value="detail">Module chi tiết</option>
                                        <option value="difference">Module khác</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Đường dẫn
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text" name="path" value="{{ old('path', ($generate->path ?? '')) }}" class="form-control" placeholder="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>- Thông tin Schema</p>
                        <p>- Lưu ý: Những trường đánh dấu <span class="text-danger">(*)</span>
                        là bắt buộc</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin chung</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Schema
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <textarea name="schema" class="form-control" style="height: 350px;" placeholder="" autocomplete="off">{{ old('schema', $generate->schema ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Schema 2
                                    </label>
                                    <textarea type="text" name="schema_2" value="{{ old('schema_2', ($generate->schema_2 ?? '')) }}" class="form-control" style="height: 350px;" placeholder="" autocomplete="off"></textarea>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

        @include('backend.dashboard.component.button')
    </div>
</form>