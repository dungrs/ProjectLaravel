<div class="col-lg-4">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Thời gian áp dụng chương trình</h5>
        </div>
        <div class="ibox-content">
            <div class="form-row mb15">
                <label for="start_date" class="control-label text-left">Ngày bắt đầu <span class="text-danger">(*)</span></label>
                <div class="form-date">
                    <input type="text" class="form-control datepicker" name="start_date" data-value="{{ old('start_date', $model->start_date ?? '') }}" autocomplete="off">
                    <span><i class="fa fa-calendar"></i></span>
                </div>
            </div>
            <div class="form-row mb15">
                <label for="end_date" class="control-label text-left">Ngày kết thúc <span class="text-danger">(*)</span></label>
                <div class="form-date">
                    <input type="text" class="form-control datepicker" name="end_date" data-value="{{ old('end_date', $model->end_date ?? '') }}" autocomplete="off">
                    <span><i class="fa fa-calendar"></i></span>
                </div>
            </div>
            <div class="form-row">
                <div class="uk-flex uk-flex-middle">
                    <input 
                        type="checkbox" 
                        name="never_end_date" 
                        value="accept" 
                        id="never_end_date"
                        @checked(old('never_end_date', $model->never_end_date ?? null) == 'accept')
                    >
                    <label for="never_end_date" class="fix-label ml5">Không có ngày kết thúc</label>
                </div>
            </div>
        </div>
    </div>
    <div class="ibox">
        <div class="ibox-title">
            <h5>Nguồn khách áp dụng</h5>
        </div>
        @php
            $sourceStatus = old('source', $model->source_status ?? null)
        @endphp

        <div class="ibox-content">
            <div class="setting-value">
                <div class="nav-setting-item uk-flex uk-flex-middle">
                    <input 
                        {{ $sourceStatus === 'all' || !$sourceStatus ? 'checked' : '' }} 
                        id="allSource" 
                        type="radio" 
                        value="all" 
                        name="source" 
                        class="chooseSource"
                    >
                    <label class="fix-label ml5" for="allSource">Áp dụng cho toàn bộ nguồn khách</label>
                </div>
                <div class="nav-setting-item uk-flex uk-flex-middle">
                    <input 
                        {{ $sourceStatus === 'choose' ? 'checked' : '' }} 
                        id="chooseSource" 
                        type="radio" 
                        value="choose" 
                        name="source" 
                        class="chooseSource"
                    >
                    <label class="fix-label ml5" for="chooseSource">Chọn nguồn khách áp dụng</label>
                </div>
            </div>

            @if ($sourceStatus === 'choose') <!-- Chỉ hiển thị nếu 'choose' được chọn -->
                <div class="source-wrapper">
                    <select name="sourceValue[]" id="sourceSelect" class="multipleSelect2" multiple>
                        @foreach ($sources as $source)
                            <option 
                                value="{{ $source->id }}" 
                                {{ in_array($source->id, old('sourceValue', [])) ? 'selected' : '' }}
                            >
                                {{ $source->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>
    <div class="ibox">
        <div class="ibox-title">
            <h5>Đối tượng áp dụng</h5>
        </div>
        @php
            $applyStatus = old('apply', $model->apply_status ?? null);
            $applyData = __('module.applyStatus');
            $applyDataFormatted = [];
            foreach ($applyData as $key => $value) {
                $applyDataFormatted[] = ['id' => $key, 'name' => $value];
            }
        @endphp
        <div class="ibox-content">
            <div class="setting-value">
                <div class="nav-setting-item uk-flex uk-flex-middle">
                    <input 
                        class="chooseApply" 
                        id="allApply" 
                        type="radio" 
                        value="all" 
                        name="apply" 
                        {{ $applyStatus === 'all' || !$applyStatus ? 'checked' : '' }} 
                    >
                    <label class="fix-label ml5" for="allApply">Áp dụng cho toàn bộ khách hàng</label>
                </div>
                <div class="nav-setting-item uk-flex uk-flex-middle">
                    <input 
                        class="chooseApply" 
                        id="chooseApply" 
                        type="radio" 
                        value="choose" 
                        name="apply"
                        {{ $applyStatus === 'choose' ? 'checked' : '' }} 
                    >
                    <label class="fix-label ml5" for="chooseApply">Chọn khách hàng áp dụng</label>
                </div>
            </div>
            @if ($applyStatus === 'choose') <!-- Chỉ hiển thị nếu 'choose' được chọn -->
                <div class="apply-wrapper">
                    <select name="applyValue[]" id="applySelect" class="multipleSelect2 conditionItem" multiple>
                        @foreach (__('module.applyStatus') as $key => $val)
                            <option 
                                value="{{ $key }}" 
                                {{ in_array($key, old('applyValue', [])) ? 'selected' : '' }}
                            >
                                {{ $val }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="wrapper-condition">
                    @if (old('applyValue') && count(old('applyValue')))
                        @foreach (old('applyValue') as $key => $val)
                            <div class="{{ $val }} wrapper-condition-item mt10">
                                <div class="mb5">
                                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                        <div class="conditionLabel">{{ $applyData[$val] ?? '' }}</div>
                                        <div class="delete">
                                            <i class="fa fa-trash"></i>
                                        </div>
                                    </div>
                                </div>
                                <select name="apply_{{ $val }}[]" class="multipleSelect2 mt10" multiple>
                                    @php
                                        $name = "apply_{$val}"; // Tạo name động
                                        $oldValues = old($name, []); // Lấy giá trị cũ hoặc mảng rỗng
                                    @endphp
                                    @foreach ($modelValue[$val] ?? [] as $item)
                                        <option 
                                            value="{{ $item['id'] }}"
                                            {{ in_array($item['id'], $oldValues) ? 'selected' : '' }}
                                        >
                                            {{ $item['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
<script>
    let applyData = @json($applyDataFormatted)
</script>