<div class="ibox">
    <div class="ibox-title">
        <h5>Chọn danh mục cha</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <select name="{module}_catalogue_id" class="form-control setupSelect2">
                        @foreach ($dropdown as $key => $val)
                            <option {{ $key == old('{module}_catalogue_id', isset(${module}->{module}_catalogue_id) ? ${module}->{module}_catalogue_id : '') ? 'selected' : '' }} value="{{ $key }}">{{ $val }}</option>
                        @endforeach 
                    </select>
                </div>
            </div>
        </div>

        @php
            $catalogues = [];
            if (isset(${module})) {
                foreach (${module}->{module}_catalogues as $key => $value) {
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
                                @if (in_array($key, old('catalogues', $catalogues, isset(${module}->{module}_catalogue_id))) && $key !== ${module}->{module}_catalogue_id)
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
                        <img src="{{ old('image', isset(${module}->image) ? asset(${module}->image) : asset('backend/img/noimage.jpg')) }}" alt="">
                    </span>
                    <input type="hidden" name="image" value="{{ old('image', (${module}->image ?? '')) }}">
                </div>
            </div>
        </div>
    </div>
</div>
@include('backend.dashboard.component.publish', ['model' => ${module} ?? null])