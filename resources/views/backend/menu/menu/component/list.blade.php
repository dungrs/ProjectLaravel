<div class="row">
    <div class="col-lg-5">
        <div class="ibox">
            <div class="ibox-content">
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" class="collapsed">Liên kết tự tạo</a>
                            </h5>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                            <div class="panel-body">
                                <div class="panel-title">Tạo Menu</div>
                                <div class="panel-description">
                                    <p>+ Cài đặt Menu mà bạn muốn hiển thị.</p>
                                    <p><small class="text-danger">* Khi khởi tạo menu bạn phải chắc chắn rằng đường dẫn menu có hoạt động. Đường dẫn trên website được khởi tạo tại các module: Bài viết, Sản phẩm, Dự án, ...</small></p>
                                    <p><small class="text-danger">* Tiêu đề và đường dẫn của menu không được bỏ trống.</small></p>
                                    <p><small class="text-danger">* Hệ thống chỉ hỗ trợ tối đa 5 cấp menu.</small></p>
                                    <a style="color: #000; border-color: #c4cdd5; display-inline-block !important;" href="" title="" class="btn btn-default add-menu m-b m-r right">Thêm đường dẫn </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @foreach (__("module.model") as $key => $val)
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#{{ $key }}" class="menu-module" data-model="{{ $key }}" aria-expanded="true">{{ $val }}</a>
                                </h4>
                            </div>
                            <div id="{{ $key }}" class="panel-collapse collapse " aria-expanded="true" style="">
                                <div class="panel-body">
                                    <form action="" class="search-model" method="get" data-model="{{ $key }}">
                                        <div class="form-row">
                                            <input type="text" value="" class="form-control search-menu" name="keyword" placeholder="Nhập 2 kí tự để tìm kiếm">
                                        </div>
                                    </form>
                                    <div class="menu-list mt20">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="ibox">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-4">
                        <label for="">Tên Menu</label>
                    </div>
                    <div class="col-lg-4">
                        <label for="">Đường dẫn</label>
                    </div>
                    <div class="col-lg-2">
                        <label for="">Vị trí</label>
                    </div>
                    <div class="col-lg-2 text-center">
                        <label for="">Xóa</label>
                    </div>
                </div>
                <div class="hr-line-dashed" style="margin: 10px 0">

                </div>
                <div class="menu-wrapper">
                    <div class="notification text-center {{ ((old('menu') || isset($menuList)) ? 'hidden' : '' ) }}" style="margin-top: 25px;">
                        <h4 style="font-weight: 500; font-size: 16px; color: #000;">
                            Danh sách liên kết này chưa có bất kì đường dẫn nào
                        </h4>
                        <p style="color: #555; margin-top: 10px;">
                            Hãy nhấn vào <span style="color: blue">"Thêm đường dẫn"</span> để bắt đầu thêm.
                        </p>
                    </div>

                    @php
                        $menus = old('menu') ?? $menuList ?? [];
                    @endphp
                    
                    @if (!empty($menus))
                        @foreach ($menus['name'] ?? [] as $key => $val)
                            <div class="row menu-item" style="margin-bottom: 20px">
                                <div class="col-lg-4">
                                    <input type="text" class="form-control" name="menu[name][]" value="{{ old('menu')['name'][$key] ?? $menus['name'][$key] }}">
                                </div>
                                <div class="col-lg-4">
                                    <input type="text" class="form-control" name="menu[canonical][]" value="{{ old('menu')['canonical'][$key] ?? $menus['canonical'][$key] }}">
                                </div>
                                <div class="col-lg-2">
                                    <input type="text" class="form-control int" name="menu[order][]" value="{{ old('menu')['order'][$key] ?? $menus['order'][$key] ?? '' }}">
                                </div>
                                <div class="col-lg-2 text-center" style="margin-top: 5px;">
                                    <a class="delete-menu text-danger"><i class="fa fa-trash"></i></a>
                                </div>
                                <input type="hidden" name="menu[id][]" value="{{ old('menu')['id'][$key] ?? $menus['id'][$key] ?? 0 }}"">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>