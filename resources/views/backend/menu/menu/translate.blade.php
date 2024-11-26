@php
    $id = $menus instanceof \Illuminate\Support\Collection
    ? $menus->firstWhere('menu_catalogue_id')?->menu_catalogue_id ?? ''
    : ($menus->id ?? '');
    $title = str_replace('{language}', $language->name, $config['seo']['translate']['title']) . ' ' . $menus->name
@endphp
@include('backend.dashboard.component.breadcrumb', ['title' => $title])

<form action="{{ route("menu.translate.save", ['languageId' => $language->id]) }}" method="post">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-4">
                <div class="uk-flex uk-flex-middle mb20">
                    @foreach ($languages as $language)
                        @php
                            $url = ($language->canonical === session('app_locale'))
                                ? route('menu.edit', ['id' => $id])
                                : route('menu.translate', [
                                    'id' => $id,
                                    'languageId' => $language->id
                                ]);
                        @endphp
                        
                        <a href="{{ $url }}">
                            <span class="image img-scaledown system-flag">
                                <img src="{{ $language->image }}" alt="">
                            </span>
                        </a>
                    @endforeach
                </div>
                <div class="panel-title">Danh sách menu</div>
                <div class="panel-description">
                    <p>+ Hệ thống tự động lấy ra các bản dịch của các Menu <span class="text-success">nếu có</span></p>
                    <p>+ Cập nhật các trường thoog tin về bản dịch cho các Menu của bạn phía bên phải <span class="text-success"></span>menu đến vị trí mong muốn</p>
                    <p>+ Lưu ý cập nhật đầy đủ thông tin <span class="text-success">Quản lý menu con</span></p>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-title">
                        <div class="uk-flex-space-between uk-flex uk-flex-middle">
                            <h5>Danh sách bản dịch</h5>
                        </div>
                    </div>
                    <div class="ibox-content">
                        @if (count($menuBuildItems))
                            @foreach ($menuBuildItems as $menu)
                                <div class="menu-translate-item">
                                    <div class="row">
                                        <div class="col-lg-12 mb10"><div class="text-danger mb10 text-bold">Menu: {{ $menu->position }}</div></div>
                                        <div class="col-lg-6">
                                            <div class="form-row">
                                                <div class="uk-flex uk-flex-middle">
                                                    <div class="menu-name">Tên Menu</div>
                                                    <input type="text" value="{{ ($menu->name) ?? '' }}" class="form-control" placeholder="" autocomplete="" disabled>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="uk-flex uk-flex-middle">
                                                    <div class="menu-name">Đường dẫn</div>
                                                    <input type="text" value="{{ ($menu->canonical) ?? '' }}" class="form-control" placeholder="" autocomplete="" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-row">
                                                <input type="text" value="{{ ($menu->translate_name) ?? '' }}" name="translate[name][]" class="form-control" placeholder="Nhập vào bản dịch của bạn" autocomplete=""">
                                            </div>
                                            <div class="form-row">
                                                <input type="text" value="{{ ($menu->translate_canonical) ?? '' }}" name="translate[canonical][]" class="form-control" placeholder="Nhập vào bản dịch của bạn" autocomplete=""">
                                                <input type="hidden" name="translate[id][]" value="{{ ($menu->id) ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>