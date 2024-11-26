<div class="row">
    <div class="col-lg-5">
        <div class="panel-head">
            <div class="panel-title">Vị trí Menu</div>
            <div class="panel-description">
                <p>- Website có các vị trí hiển thị cho từng menu</p>
                <p>- Lựa chọn vị trí mà bạn muốn hiển thị</p>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="ibox">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between mb15">
                            <div class="text-bold">
                                Chọn vị trí hiển thị
                                <span class="text-danger">(*)</span>
                            </div>
                            <button data-toggle="modal" data-target="#createMenuCatalogue" type="button" name="" class="addMenuCatalogue btn btn-danger">Tạo vị trí hiển thị</button>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <select class="setupSelect2 select-2-container" style="margin-right: 0px !important; width: 100%" name="menu_catalogue_id">
                            <option value="none">[Chọn vị trí hiển thị]</option>
                            @if (isset($menuCatalogues) && count($menuCatalogues) > 0)
                                @foreach ($menuCatalogues as $menuCatalogue)
                                    <option {{ (isset($menu->catalogue_id) && $menuCatalogue->id == $menu->menu_catalogue_id) ? 'selected' : '' }} value="{{ $menuCatalogue->id }}">{{ $menuCatalogue->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>