<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('messages.parent') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <p class="text-danger notice ">*{{ __('messages.parentNotice') }}</p>
                    <select name="parent_id" class="form-control setupSelect2" id="">
                        @foreach ($dropdown as $key => $val)
                            <option {{ $key == old('parent_id', isset($attributeCatalogue->parent_id) ? $attributeCatalogue->parent_id : '') ? 'selected' : '' }} value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('messages.image') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="image img-cover image-target">
                        <img src="{{ old('image', isset($attributeCatalogue->image) ? asset($attributeCatalogue->image) : asset('backend/img/noimage.jpg')) }}" alt="">
                    </span>
                    <input type="hidden" name="image" value="{{ old('image', ($attributeCatalogue->image ?? '')) }}">
                </div>
            </div>
        </div>
    </div>
</div>
@include('backend.dashboard.component.publish', ['model' => $attributeCatalogue ?? null])