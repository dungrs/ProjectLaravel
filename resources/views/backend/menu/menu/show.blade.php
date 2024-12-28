@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['show']['title']])
@include('backend.dashboard.component.formError')

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-4">
            <div class="uk-flex uk-flex-middle mb20">
                @foreach ($languages as $language)
                    @php
                        $id = $menus instanceof \Illuminate\Support\Collection
                            ? $menus->firstWhere('menu_catalogue_id')?->menu_catalogue_id ?? ''
                            : ($menus->id ?? '');
                    
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
                <p>+ Danh sách menu giúp bạn dễ dàng kiểm soát bố cục menu. Bạn có thể thêm mới hoặc cập nhật menu bằng nút <span class="text-success">Cập nhật Menu</span></p>
                <p>+ Bạn có thể thay đổi vị trí hiển thị của menu bằng cách <span class="text-success"></span> menu đến vị trí bạn muốn</p>
                <p>+ Dễ dàng khởi tạo menu con bằng cách ấn vào nút <span class="text-success">Quản lý menu con</span></p>
                <p class="text-danger">+ Hỗ trọ tới danh mục con tới cấp 5</p>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>
                        @if ($menus instanceof \Illuminate\Support\Collection)
                            {{ $menus->firstWhere('menu_catalogue_name')?->menu_catalogue_name ?? '' }}
                        @else
                            {{ $menus->name ?? '' }}
                        @endif
                    </h5>                        
                    <a href="{{ route('menu.editMenu', $menus instanceof \Illuminate\Support\Collection ? ($menus->firstWhere('menu_catalogue_id')?->menu_catalogue_id ?? '') : ($menus->id ?? '')) }}" class="custom-button">Cập nhật Menu Cấp 1</a>
                </div>
                <div class="ibox-content">
                    @if (count($menuList) > 0)
                    <div class="dd" id="nestable2" data-menuCatalogueId="{{ $menus instanceof \Illuminate\Support\Collection ? ($menus->firstWhere('menu_catalogue_id')?->menu_catalogue_id ?? '') : ($menus->id ?? '') }}">
                            {!! recursiveMenuHtml($menuList) !!}
                        </div>
                    @endif
                    <textarea id="nestable2-output" class="form-control hidden" style="width: 472px; height: 296px;"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>