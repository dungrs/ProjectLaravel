<div class="perpage">
    <div class="uk-flex uk-flex-space-between uk-flex-middle">
        @php
            $perpage = request('perpage') ?: old("perpage");
        @endphp
        <select name="perpage" class="form-control input-sm perpage filter" id="">
            @for ($i = 20; $i <= 200; $i+=20)
                <option {{ ($perpage == $i) ? 'selected' : '' }} value=" {{ $i }}">{{ $i }} {{ __('messages.perpage') }}</option>
            @endfor
        </select>
    </div>
</div>