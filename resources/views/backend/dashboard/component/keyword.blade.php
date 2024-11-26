<div class="uk-search uk-flex uk-flex-middle mr10">
    <div class="input-group">
        <input 
            type="text"
            name="keyword"
            value="{{ request("keyword") ?: old('keyword') }}"
            placeholder="{{ __('messages.searchInput') }}"
            class="form-control"    
            >
        <span class="input-group-btn">
            <button type="submit" name="search" value="search"
                    class="btn btn-primary mb-0 btn-sm">{{ __('messages.search') }}
            </button>
        </span>
    </div>
</div>