<div id="createMenuCatalogue" class="modal fade" tabindex="-1" role="dialog">
    <form action="" class="form create-menu-catalogue" method="">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Thêm mới vị trí hiển thị của menu</h4>
                    <small class="font-bold">Nhập đầy đủ thông tin để hiển thị vị trí của menu.</small>
                </div>
                <div class="modal-body">
                    <div class="form-error alert hidden">

                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb15">
                            <label for="menuPositionName">Tên vị trí hiển thị</label>
                            <input type="text" class="form-control" id="menuPositionName" name="name" placeholder="Nhập tên vị trí">
                            <div class="error name"></div>
                        </div>
                        <div class="col-lg-12" style="margin-bottom: 10px;">
                            <label for="menuKeyword">Từ khóa</label>
                            <input type="text" class="form-control" id="menuKeyword" name="keyword" placeholder="Nhập từ khóa">
                            <div class="error keyword"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" name="create" value="create" class="btn btn-primary">Lưu lại</button>
                </div>
            </div>
        </div>
    </form>
</div>