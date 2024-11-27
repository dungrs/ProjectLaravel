@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('widget.store') : route("widget.update", $widget->id)
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin widget</h5>
                    </div>
                    <div class="ibox-content widgetContent">
                        @include('backend.dashboard.component.content', ['offTitle' => true, 'offContent' => true])
                    </div>
                </div>
                @include('backend.dashboard.component.album')
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Cấu hình nội dung widget</h5>
                    </div>
                    <div class="ibox-content module-list">
                        <div class="labelText">
                            Chọn Module
                        </div>
                        @foreach (__('module.model') as $key => $val)
                            <div class="model-item uk-flex uk-flex-middle">
                                <input type="radio" id="{{ $key }}" class="input-radio" value="{{ $key }}" name="model">
                                <label for="{{ $key }}">{{ $val }}</label>
                            </div>
                        @endforeach

                        <div class="search-model-box">
                            <i class="fa fa-search icon-widget-search"></i>
                            <input type="text" class="form-control search-model">

                            <div class="ajax-search-result">
                                @for ($i = 0; $i < 10; $i++)
                                    <button class="ajax-search-item">
                                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                            <span>HÌNH THỨC THANH TOÁN KHI DẶT HÀNG ONLINE</span>
                                            <div class="auto-icon">
                                                <img class="check-icon-widget icon-widget" src="{{ asset('backend/img/check.png') }}" alt="">
                                            </div>
                                        </div>
                                    </button>
                                @endfor
                            </div>
                        </div>

                        <div class="search-model-result">
                            @for ($i = 0; $i < 10; $i++)
                                <div class="search-result-item">
                                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                        <div class="uk-flex uk-flex-middle">
                                            <span class="image img-cover">
                                                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRrfCs7RwnUpivnANUJaLoN6Q-wBvkOkHwmlg&s" alt="">
                                            </span>
                                            <span class="name">HÌNH THỨC THANH TOÁN KHI DẶT HÀNG ONLINE</span>
                                        </div>
                                        <div class="deleted">
                                            <img class="deleted-icon-widget icon-widget" src="{{ asset('backend/img/close.png') }}" alt="">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                    </div>
                </div>
            </div>
            @include('backend.widget.widget.component.aside')
        </div>

        <div class="text-right mb15">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>