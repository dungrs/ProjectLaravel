<form action="{{ route('{module}.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.filterPublish')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @php
                        $publish = request('publish') ? : old('publish');
                        ${module}CatalogueId = request('{module}_catalogue_id') ? : old('{module}_catalogue_id');
                    @endphp
                    <select name="publish" class="form-control mr10 setupSelect2" id="">
                        @foreach (__('messages.publish') as $key => $val)
                            <option {{ ($publish == $key) ? 'selected' : '' }} value="{{ $key }}" > {{ $val }} </option>
                        @endforeach
                    </select>
                    <select name="{module}_catalogue_id" class="form-control mr10 setupSelect2" id="">
                        @foreach ($dropdown as $key => $val)
                            <option {{ (${module}CatalogueId == $key) ? 'selected' : '' }} value="{{ $key }}" > {{ $val }} </option>
                        @endforeach
                    </select>
                    @include('backend.dashboard.component.keyword')
                    <a href="{{ route("{module}.create") }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i> {{ __('messages.{module}.create.title') }}</a>
                </div>
            </div>
        </div>
    </div>
</form>