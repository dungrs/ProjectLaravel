@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@php
    $url = isset($config['method']) && $config['method'] === 'translate' 
        ? route('system.save.translate', ['languageId' => $languageId]) 
        : route('system.store');
@endphp
<form method="post" action="{{ $url }}" class="box">
    <div class="ibox-content">
        @csrf
        <div class="uk-flex uk-flex-middle">
            @foreach ($languages as $language)
                @if ($language->canonical === session('app_locale'))
                    @continue
                @endif
                <a href="{{ route('system.translate', ['languageId' => $language->id]) }}">
                    <span class="image img-scaledown system-flag">
                        <img src="{{ $language->image }}" alt="">
                    </span>
                </a>
            @endforeach
        </div>
        @foreach ($systemConfig as $key => $val)
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="panel-head">
                            <div class="panel-title">{{ $val['label'] }}</div>
                            <div class="panel-description">
                                <p>- {{ $val['description'] }}</p>
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
                            @if (count($val['value']))
                                <div class="ibox-content">
                                    @foreach ($val['value'] as $keyVal => $item)
                                    @php
                                        $name = $key . '_' . $keyVal
                                    @endphp
                                        <div class="row mb15">
                                            <div class="col-lg-12">
                                                <div class="form-row">
                                                    <label for="" class="control-label uk-flex uk-flex-space-between">
                                                        <span>{{ $item['label'] }}</span>
                                                        {!! renderSystemLink($item, $systems) !!}
                                                        {!! renderSystemTitle($item, $systems) !!}
                                                    </label>
                                                    @switch($item['type'])
                                                        @case('text')
                                                            {!! renderSystemInput($name, $systems) !!}
                                                            @break
                                                        @case('image')
                                                            {!! renderSystemImage($name, $systems) !!}
                                                            @break
                                                        @case('textarea')
                                                            {!! renderSystemTextarea($name, $systems) !!}
                                                            @break
                                                        @case('select')
                                                            {!! renderSystemSelect($item, $name, $systems) !!}
                                                            @break
                                                        @case('editor')
                                                            {!! renderSystemEditor($name, $systems) !!}
                                                            @break
                                                        @break
                                                    @endswitch

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="text-right mb15" style="padding-bottom: 40px;">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>

<script>
    var province_id = '{{ (isset($user->province_id)) ? $user->province_id : old('province_id') }}';
    var district_id = '{{ (isset($user->district_id)) ? $user->district_id : old('district_id') }}';
    var ward_id = '{{ (isset($user->ward_id)) ? $user->ward_id : old('ward_id') }}'
</script>