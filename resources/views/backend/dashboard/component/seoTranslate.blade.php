<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('messages.seo') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="seo-container mb15">
            <div class="meta-title">{{ old('meta_title', ($model->meta_title ?? __('messages.seoTitle'))) }}</div>
            <div class="canonical">
                {{ old('canonical', isset($model->canonical)) ? url('/') . '/' . old('canonical', $model->canonical) . config('apps.general.suffix') : __('messages.seoCanonical') }}
            </div>
            <div class="meta-description">
                {{ old('meta_description', ($model->meta_description ?? __('messages.seoDescription'))) }}
            </div>
        </div>
        <div class="seo-wrapper">
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left" style="width: 100%;">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                <span>{{ __('messages.seoMetaTitle') }}</span>
                                <span class="count_meta-title">0 {{ __('messages.character') }}</span>
                            </div>
                        </label>
                        <input type="text" value="{{ old('meta_title', ($model->meta_title ?? '')) }}" name="translate_meta_title" value="" class="form-control" placeholder="" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left" style="width: 100%;">
                            <span>{{ __('messages.seoMetaKeyword') }}</span>
                        </label>
                        <input type="text" value="{{ old('meta_keyword', ($model->meta_keyword ?? '')) }}" name="translate_meta_keyword" value="" class="form-control" placeholder="" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left" style="width: 100%;">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                <span>{{ __('messages.seoMetaDescription') }}</span>
                                <span class="count_meta-description">0 {{ __('messages.character') }}</span>
                            </div>
                        </label>
                        <textarea value="{{ old('meta_description', ($model->meta_description ?? '')) }}" type="text" name="translate_meta_description" value="" class="form-control" placeholder="" autocomplete="off">{{ old('meta_description', ($model->meta_description ?? '')) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left" style="width: 100%;">
                            <span>{{ __('messages.canonical') }}<span class="text-danger">*</span> </span>
                        </label>
                        <div class="input-wrapper">
                            <input type="text" name="translate_canonical" value="{{ old('canonical', ($model->canonical ?? '')) }}" class="form-control seo-canonical" placeholder="" autocomplete="off">
                            <span class="baseUrl">{{ url('/') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>