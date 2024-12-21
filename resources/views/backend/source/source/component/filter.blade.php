<form action="{{ route('source.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @php
                        $publish = request('publish') ?: old('publish')
                    @endphp
                    <select name="publish" class="form-control mr10 setupSelect2" id="">
                        @foreach (config("apps.general.publish") as $key => $val)
                            <option {{ ($publish == $key) ? 'selected' : '' }} value="{{ $key }}" > {{ $val }} </option>
                        @endforeach
                    </select>
                    @include('backend.dashboard.component.keyword')
                    <a href="{{ route("source.create") }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>
                    Thêm mới nguồn khách</a>
                </div>
            </div>
        </div>
    </div>
</form>