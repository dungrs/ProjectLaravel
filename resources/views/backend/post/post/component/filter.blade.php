<form action="{{ route('post.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <div class="perpage">
                <div class="uk-flex uk-flex-space-between uk-flex-middle">
                    @php
                        $perpage = request('perpage') ?: old("perpage");
                    @endphp
                    <select name="perpage" class="form-control input-sm perpage filter" id="">
                        @for ($i = 20; $i <= 200; $i+=20)
                            <option {{ ($perpage == $i) ? 'selected' : '' }} value=" {{ $i }}">{{ $i }} {{ __("messages.perpage") }}  </option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @php
                        $publish = request('publish') ? : old('publish');
                        $postCatalogueId = request('post_catalogue_id') ? : old('post_catalogue_id');
                    @endphp
                    <select name="publish" class="form-control mr10 setupSelect2" id="">
                        @foreach (__('messages.publish') as $key => $val)
                            <option {{ ($publish == $key) ? 'selected' : '' }} value="{{ $key }}" > {{ $val }} </option>
                        @endforeach
                    </select>
                    <select name="post_catalogue_id" class="form-control mr10 setupSelect2" id="">
                        @foreach ($dropdown as $key => $val)
                            <option {{ ($postCatalogueId == $key) ? 'selected' : '' }} value="{{ $key }}" > {{ $val }} </option>
                        @endforeach
                    </select>
                    <div class="uk-search uk-flex uk-flex-middle mr10">
                        <div class="input-group">
                            <input 
                                type="text"
                                name="keyword"
                                value="{{ request("keyword") ?: old('keyword') }}"
                                placeholder=" {{ __('messages.searchInput') }}"
                                class="form-control"    
                                >
                            <span class="input-group-btn">
                                <button type="submit" name="search" value="search"
                                        class="btn btn-primary mb-0 btn-sm"> {{ __('messages.search') }}
                                </button>
                            </span>
                        </div>
                    </div>
                    <a href="{{ route("post.create") }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i> {{ __('messages.post.create.title') }}</a>
                </div>
            </div>
        </div>
    </div>
</form>