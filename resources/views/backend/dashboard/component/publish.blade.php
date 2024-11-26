<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('messages.advange') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <div class="mb15">
                        <select name="publish" class="form-control setupSelect2" id="">
                            @foreach (config('apps.general.publish') as $key => $val )
                                <option {{ $key == old('publish', isset($model->publish) ? $model->publish : '') ? 'selected' : '' }} value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select> 
                    </div>
                    <div class="">
                        <select name="follow" class="form-control setupSelect2" id="">
                            @foreach (config('apps.general.follow') as $key => $val )
                                <option {{ $key == old('follow', isset($model->follow) ? $model->follow : '') ? 'selected' : '' }} value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>