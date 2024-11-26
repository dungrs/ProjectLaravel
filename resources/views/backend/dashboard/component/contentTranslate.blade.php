<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-label text-left">{{ __('messages.title') }}
                <span class="text-danger">(*)</span>
            </label>
            <input type="text" name="translate_name" value="{{ old('name', ($model->name ?? '')) }}" class="form-control" placeholder="" autocomplete="off">
        </div>
    </div>
</div>
<div class="row mb30">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-label text-left"> {{ __('messages.description') }}
            </label>
            <textarea type="text" name="translate_description" value="{{ old('description', ($model->description ?? '')) }}" class="form-control ck-editor" placeholder="" autocomplete="off" id="ckDescription_1" data-height="150">{{ old('meta_description', ($model->meta_description ?? '')) }}</textarea>
        </div>
    </div>
</div>
<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <div class="uk-flex uk-flex-middle uk-flex-space-between mb15">
                <label for="" class="control-label text-left">{{ __('messages.content') }}</label>
                <a class="multipleUploadImageCkeditor" data-target='ckContent_1'>{{ __('messages.upload') }}</a>
            </div>
            <textarea type="text" name="translate_content" value="{{ old('content', ($model->content ?? '')) }}" class="form-control ck-editor" placeholder="" autocomplete="off" id="ckContent_1" data-height="500">{{ old('content', ($model->content ?? '')) }}</textarea>
        </div>
    </div>
</div>