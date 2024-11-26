<div class="ibox">
    <div class="ibox-title">
        <h5>Chọn danh mục cha</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <select name="post_catalogue_id" class="form-control setupSelect2">
                        @foreach ($dropdown as $key => $val)
                            <option {{ $key == old('post_catalogue_id', isset($post->post_catalogue_id) ? $post->post_catalogue_id : '') ? 'selected' : '' }} value="{{ $key }}">{{ $val }}</option>
                        @endforeach 
                    </select>
                </div>
            </div>
        </div>

        @php
            $catalogues = [];
            if (isset($post)) {
                foreach ($post->post_catalogues as $key => $value) {
                    $catalogues[] = $value->id;
                }
            }
        @endphp

        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="control-label">Danh mục phụ</label>
                    <select multiple name="catalogue[]" class="form-control setupSelect2" id="">
                        @foreach ($dropdown as $key => $val)
                            <option value="{{ $key }}"
                                @if (in_array($key, old('catalogues', $catalogues, isset($post->post_catalogue_id))) && $key !== $post->post_catalogue_id)
                                    selected
                                @endif
                            >{{ $val }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-title">
        <h5>Chọn ảnh đại diện</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="image img-cover image-target">
                        <img src="{{ old('image', isset($post->image) ? asset($post->image) : asset('backend/img/noimage.jpg')) }}" alt="">
                    </span>
                    <input type="hidden" name="image" value="{{ old('image', ($post->image ?? '')) }}">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-title">
        <h5>CẤU HÌNH NÂNG CAO</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <div class="mb15">
                        <select name="publish" class="form-control setupSelect2" id="">
                            @foreach (config('apps.general.publish') as $key => $val )
                                <option {{ $key == old('publish', isset($post->publish) ? $post->publish : '') ? 'selected' : '' }} value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select> 
                    </div>
                    <div class="">
                        <select name="follow" class="form-control setupSelect2" id="">
                            @foreach (config('apps.general.follow') as $key => $val )
                                <option {{ $key == old('follow', isset($post->follow) ? $post->follow : '') ? 'selected' : '' }} value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>