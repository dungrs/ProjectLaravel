<form action="{{ route('generate.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <div class="perpage">
                <div class="uk-flex uk-flex-space-between uk-flex-middle">
                    @php
                        $perpage = request('perpage') ?: old("perpage");
                    @endphp
                    <select name="perpage" class="form-control input-sm perpage filter" id="">
                        @for ($i = 20; $i <= 200; $i+=20)
                            <option {{ ($perpage == $i) ? 'selected' : '' }} value=" {{ $i }}">{{ $i }} bản ghi</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    <div class="uk-search uk-flex uk-flex-middle mr10">
                        <div class="input-group">
                            <input 
                                type="text"
                                name="keyword"
                                value="{{ request("keyword") ?: old('keyword') }}"
                                placeholder="Nhập từ khóa bạn muốn kiếm"
                                class="form-control"    
                                >
                            <span class="input-group-btn">
                                <button type="submit" name="search" value="search"
                                        class="btn btn-primary mb-0 btn-sm">Tìm kiếm
                                </button>
                            </span>
                        </div>
                    </div>
                    <a href="{{ route("generate.create") }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>
                    Tạo Module mới</a>
                </div>
            </div>
        </div>
    </div>
</form>