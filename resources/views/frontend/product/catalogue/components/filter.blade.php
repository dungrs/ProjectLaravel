<div class="filter">
    <div class="uk-flex uk-flex-space-between uk-flex-middle">
        <div class="filter-widget">
            <div class="uk-flex uk-flex-middle">
                <a href="" class="view-grid active">
                    <i class="fi-rs-grid"></i>
                </a>
                <a href="" class="view-grid view-list">
                    <i class="fi-rs-list"></i>
                </a>
                <div class="filter-button ml10 mr20">
                    <a href="" class="btn-filter uk-flex uk-flex-middle">
                        <i class="fi-rs-filter mr5"></i>
                        <span>Bộ lọc</span>
                    </a>
                </div>
                <div class="perpage uk-flex uk-flex-middle">
                    <div class="filter-text">Hiển thị</div>
                    <select name="perpage" class="nice-select" id="perpage">
                        @for ($i = 10; $i <= 100; $i+= 20)
                            <option value="{{ $i }}">{{ $i }} sản phẩm</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
        <div class="sorting">
            <select name="sort" id="" class="nice-select filtering" style="">
                <option value="">Lọc theo kết quả</option>
                <option value="price:desc">Giá: Từ cao đến thấp</option>
                <option value="price:asc">Giá: Từ thấp đến cao</option>
                <option value="title:asc">Tên sản phẩm A - Z</option>
                <option value="title:desc">Tên sản phẩm Z - A</option>
            </select>
        </div>
    </div>
</div>