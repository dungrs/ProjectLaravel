<div class="col-lg-9">
    <div class="ibox">
        <div class="ibox-title">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <h5>Danh sách slides</h5>
                <button type="button" class="addSlide btn">Thêm slide</button>
            </div>
        </div>
        @php
            $slides = old('slide', isset($slideItem) ? $slideItem : null);
        @endphp
        <div class="ibox-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row slide-list">
                        <div class="text-danger slide-notification {{ empty($slides) ? '' : 'hidden' }}">
                            Chưa có hình ảnh nào được chọn ...
                        </div>
                        @if(isset($slides) && count($slides) > 0)
                            @foreach ($slides['image'] as $key => $val)
                                @php
                                    $image = $val;
                                    $description = $slides['description'][$key] ?? '';
                                    $name = $slides['name'][$key] ?? '';
                                    $canonical = $slides['canonical'][$key] ?? '';
                                    $alt = $slides['alt'][$key] ?? '';
                                    $window = isset($slides['window'][$key])? $slides['window'][$key] : '';
                                @endphp
                                <div class="ui-state-default" style="width: 100% !important; border: none;">
                                    <div class="row mb20">
                                        <div class="col-lg-3 custom-row">
                                            <span class="slide-image img-cover">
                                                <img src="{{ $image }}" alt="">
                                                <input type="hidden" name="slide[image][]" value="{{ $image }}">
                                                <span class="deleteSlide btn btn-danger"><i class="fa fa-trash"></i></span>
                                            </span>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="tabs-container">
                                                <ul class="nav nav-tabs">
                                                    <li class="active">
                                                        <a data-toggle="tab" href="#tab-info-{{ $key }}" aria-expanded="true">Thông tin chung</a>
                                                    </li>
                                                    <li>
                                                        <a data-toggle="tab" href="#tab-seo-{{ $key }}" aria-expanded="false">SEO</a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content">
                                                    <div id="tab-info-{{ $key }}" class="tab-pane active">
                                                        <div class="panel-body">
                                                            <label class="slide-label label-text mb10">Mô tả</label>
                                                            <div class="form-row">
                                                                <textarea name="slide[description][]" class="form-control">{{ $description }}</textarea>
                                                            </div>
                                                            <div class="form-row form-row-url slide-seo-tab">
                                                                <input type="text" name="slide[canonical][]" class="form-control" placeholder="URL" value="{{ $canonical }}">
                                                                <div class="overlay">
                                                                    <div class="uk-flex uk-flex-middle">
                                                                        <label for="window_{{ $key }}">Mở trong tab mới</label>
                                                                        <input id="window_{{ $key }}" {{ $window == '_blank' ? 'checked' : '' }} type="checkbox" name="slide[window][]" value="_blank">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="tab-seo-{{ $key }}" class="tab-pane">
                                                        <div class="panel-body">
                                                            <div>
                                                                <div class="form-row form-row-url slide-neo-tab" style="margin-top: 0 !important">
                                                                    <label class="slide-label label-text mb10">Tiêu đề ảnh</label>
                                                                    <input type="text" name="slide[name][]" class="form-control" placeholder="Tiêu đề ảnh" value="{{ $name }}">
                                                                </div>
                                                            </div>
                                                            <div style="margin-top: 10px">
                                                                <div class="form-row form-row-url slide-neo-tab">
                                                                    <label class="slide-label label-text">Mô tả ảnh</label>
                                                                    <input type="text" name="slide[alt][]" class="form-control" placeholder="Mô tả ảnh" value="{{ $alt }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            @endforeach
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>