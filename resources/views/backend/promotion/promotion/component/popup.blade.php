<div id="findProduct" class="modal fade" tabindex="-1" role="dialog">
    <form action="" class="form create-menu-catalogue" method="">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Chọn sản phẩm</h4>
                    <small class="font-bold text-navy">Chọn sản phẩm sẵn có hoặc tìm kiếm theo sản phẩm mà bạn mong muốn</small>
                </div>
                <div class="modal-body">
                    <div class="search-model-box mb20">
                        <i class="fa fa-search icon-widget-search"></i>
                        <input name="keyword" type="text" class="form-control search-model" placeholder="Tìm kiếm theo tên, mã sản phẩm, SKU, ...">
                    </div>
                    <div class="search-list">
                        @for ($i = 0; $i < 20; $i++)
                            <div class="search-object-item">
                                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                    <div class="object-info">
                                        <div class="uk-flex uk-flex-middle">
                                            <div class="uk-flex uk-flex-middle">
                                                <input type="checkbox" name="" value="" class="input-checkbox">
                                            </div>
                                            <span class="img img-scaledown">
                                                <img src="https://pbs.twimg.com/media/GQQN5BBbsAAH4Fu?format=jpg&name=4096x4096" alt="">
                                            </span>
                                            <div class="object-name">
                                                <div class="name">
                                                    Macbook Pro 2023 phiên bản xanh vàng đỏ
                                                </div>
                                                <div class="jscode">Mã SP: 12313213</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="object-extra-info">
                                        <div class="price">1.200.000</div>
                                        <div class="object-inventory">
                                            <div class="uk-flex uk-flex-middle">
                                                <span class="text-1">Tồn kho: </span>
                                                <span class="text-value">10.000</span>
                                                <span class="text-1 slash">|</span>
                                                <span class="text-value">Có thể bán: 9000</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" name="create" value="create" class="btn btn-success">Xác nhận</button>
                </div>
            </div>
        </div>
    </form>
</div>