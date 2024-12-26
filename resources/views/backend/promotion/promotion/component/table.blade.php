<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>Tên chương trình</th>
        <th>Chiết khấu</th>
        <th>Thông tin</th>
        <th>Ngày bắt đầu</th>
        <th>Ngày kết thúc</th>
        <th class="text-center">Tình trạng</th>
        <th class="text-center">Thao tác</th>
    </tr>
    </thead>
    <tbody>
        @if(isset($promotions) && is_object($promotions))
            @foreach ($promotions as $promotion)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $promotion->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <div>
                            <span>{{ $promotion->name }}</span>
                            @if($promotion->end_date && \Carbon\Carbon::parse($promotion->end_date)->isPast())
                                <span class="text-danger">- Hết Hạn</span>
                            @endif
                        </div>
                        <div class="text-small text-success">{{ $promotion->code }}</div>
                    </td>
                    <td>
                        <div class="discount-information text-center">
                            {!! renderDiscountInformation($promotion) !!}
                        </div>
                    </td>
                    <td>
                        <div>{{ __('module.promotion')[$promotion->method] }}</div>
                    </td>
                    <td>
                        {{ $promotion->start_date ? convertDateTime($promotion->start_date) : 'Không xác định' }}
                    </td>
                    <td>
                        {{ $promotion->end_date ? (\Carbon\Carbon::parse($promotion->end_date)->isPast() ? 'Hết Hạn' : convertDateTime($promotion->end_date)) : 'Không giới hạn' }}
                    </td>
                    <td class="text-center">
                        <input value="{{ $promotion->publish }}" {{ ($promotion->publish == 2) ? 'checked' : '' }} type="checkbox" class="js-switch status js-switch-{{ $promotion->id }}" data-field="publish" data-model="{{ $config['model'] }}" data-model-id="{{ $promotion->id }}">
                    </td>
                    <td class="text-center">
                        <a href="{{ route("promotion.edit", $promotion->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route("promotion.delete", $promotion->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>   
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $promotions-> links('pagination::bootstrap-4') }}