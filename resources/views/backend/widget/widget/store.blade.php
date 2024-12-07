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
                        @include('backend.dashboard.component.content', ['offTitle' => true, 'offContent' => true, 'model' => ($widget) ?? null])
                    </div>
                </div>
                @include('backend.dashboard.component.album', ['model' => $widget ?? null])
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
                                <input type="radio" id="{{ $key }}" class="input-radio" value="{{ $key }}" name="model" {{ (old('model', ($widget->model) ?? null) == $key) ? 'checked' : '' }}>
                                <label for="{{ $key }}">{{ $val }}</label>
                            </div>
                        @endforeach

                        <div class="search-model-box">
                            <i class="fa fa-search icon-widget-search"></i>
                            <input name="keyword" type="text" class="form-control search-model">

                            <div class="ajax-search-result">
                               
                            </div>
                        </div>

                        @php
                            $modelItem = old('modelItem', ($widgetItem) ?? [])   
                        @endphp

                        <div class="search-model-result">
                            @if (count($modelItem) > 0)
                                @foreach ($modelItem['id'] as $key => $val)
                                    <div class="search-result-item" data-canonical="{{ $modelItem['canonical'][$key] }}">
                                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                            <div class="uk-flex uk-flex-middle">
                                                <span class="image img-cover">
                                                    <img src="{{ $modelItem['image'][$key] === null ? 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRrfCs7RwnUpivnANUJaLoN6Q-wBvkOkHwmlg&s' : $modelItem['image'][$key] }}" alt="">
                                                </span>
                                                <span class="name">{{ $modelItem['name'][$key] }}</span>
                                                <div class="hidden">
                                                    <input type="text" name="modelItem[id][]" value="{{ $modelItem['id'][$key] }}">
                                                    <input type="text" name="modelItem[name][]" value="{{ $modelItem['name'][$key] }}">
                                                    <input type="text" name="modelItem[image][]" value="{{ $modelItem['image'][$key] }}">
                                                    <input type="text" name="modelItem[canonical][]" value="{{ $modelItem['canonical'][$key] }}">
                                                </div>
                                            </div>
                                            <div class="deleted">
                                                <img class="deleted-icon-widget icon-widget" src="{{ asset('backend/img/close.png') }}" alt="">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
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

<script>
    const checkIconPath = "{{ asset('backend/img/check.png') }}";
    const deleteIconPath = "{{ asset('backend/img/close.png') }}"
</script>