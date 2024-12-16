@if (!isset($offTitle))
<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-label text-left">{{ __('messages.title') }}
                <span class="text-danger">(*)</span>
            </label>
            <input type="text" name="name" value="{{ old('name', ($model->name ?? '')) }}" class="form-control" placeholder="" autocomplete="off" {{ (isset($disabled)) ? 'disabled' : '' }}>
        </div>
    </div>
</div>
@endif
<div class="row mb30">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-label text-left"> {{ __('messages.description') }}
            </label>
            <textarea 
                type="text" 
                name="description" 
                value="{{ old('description', ($model->description ?? '')) }}" 
                class="form-control ck-editor" 
                placeholder="" 
                autocomplete="off" 
                id="description" 
                data-height="150" 
                {{ (isset($disabled)) ? 'disabled' : '' }}
            >{{ old('description', ($model->description ?? '')) }}
            </textarea>
        </div>
    </div>
</div>
@if (!isset($offContent))
<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <div class="uk-flex uk-flex-middle uk-flex-space-between mb15">
                <label for="" class="control-label text-left">{{ __('messages.content') }}</label>
                <a class="multipleUploadImageCkeditor" data-target='ckContent'>{{ __('messages.upload') }}</a>
            </div>
            <textarea type="text" name="content" class="form-control ck-editor" placeholder="" autocomplete="off" id="ckContent" data-height="500" {{ isset($disabled) ? 'disabled' : '' }}>
                {{ old('content', !empty($model->content) ? $model->content : ' ') }}</textarea>
        </div>
    </div>
</div>
@endif