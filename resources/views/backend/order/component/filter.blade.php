<form action="{{ route('order.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <div class="uk-flex uk-flex-middle">
                @include('backend.dashboard.component.perpage')
                <div class="date-item-box">
                    <input type="type" name="created_at" value="{{ request('created_at') ? : old('created_at') }}" class="rangepicker form-control">
                </div>
            </div>
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    <div class="mr10">
                        @foreach (__('cart') as $key => $val)
                            @php
                                $currentValue = request($key) ?? old($key, '');
                            @endphp
                            <select name="{{ $key }}" class="form-control setupSelect2" id="{{ $key }}">
                                @foreach ($val as $index => $item)
                                    <option value="{{ $index }}" {{ $currentValue == $index ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select> 
                        @endforeach
                    </div>
                    @include('backend.dashboard.component.keyword')
                </div>
            </div>
        </div>
    </div>
</form>