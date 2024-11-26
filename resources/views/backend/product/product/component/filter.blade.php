<form action="{{ route('product.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.filterPublish')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @php
                        $publish = request('publish') ? : old('publish');
                        $productCatalogueId = request('product_catalogue_id') ? : old('product_catalogue_id');
                    @endphp
                    <select name="publish" class="form-control mr10 setupSelect2" id="">
                        @foreach (__('messages.publish') as $key => $val)
                            <option {{ ($publish == $key) ? 'selected' : '' }} value="{{ $key }}" > {{ $val }} </option>
                        @endforeach
                    </select>
                    <select name="product_catalogue_id" class="form-control mr10 setupSelect2" id="">
                        @foreach ($dropdown as $key => $val)
                            <option {{ ($productCatalogueId == $key) ? 'selected' : '' }} value="{{ $key }}" > {{ $val }} </option>
                        @endforeach
                    </select>
                    @include('backend.dashboard.component.keyword')
                    <a href="{{ route("product.create") }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i> {{ __('messages.product.create.title') }}</a>
                </div>
            </div>
        </div>
    </div>
</form>