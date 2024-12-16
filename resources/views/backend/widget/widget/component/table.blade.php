<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>Tên Widget</th>
        <th>Từ khóa</th>
        @include('backend.dashboard.component.languageTh')
        <th class="text-center">Tình trạng</th>
        <th class="text-center">Thao tác</th>
    </tr>
    </thead>
    <tbody>
        @if(isset($widgets) && is_object($widgets))
            @foreach ($widgets as $widget)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $widget->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        {{ $widget -> name }}
                    </td>
                    <td>
                        {{ $widget -> keyword }}
                    </td>
                        @foreach ($languages as $language)
                            @if ($language->canonical === session('app_locale'))
                                @continue
                            @endif

                            @php
                                $translated = isset($widget->description[$language->id]) ? 1 : 0 
                            @endphp

                            <td class="text-center">
                                <a 
                                    href="{{ route('widget.translate', ['languageId' => $language->id, 'id' => $widget->id]) }}"
                                    class="{{ $translated == 1 ? 'text-danger' : '' }}"
                                >{{ $translated == 1 ? 'Đã dịch' : 'Chưa dịch' }}</a>
                            </td>
                        @endforeach
                    <td class="text-center">
                        <input value="{{ $widget->publish }}" {{ ($widget->publish == 2) ? 'checked' : '' }} type="checkbox" class="js-switch status js-switch-{{ $widget->id }}" data-field="publish" data-model="{{ $config['model'] }}" data-model-id="{{ $widget->id }}">
                    </td>
                    <td class="text-center">
                        <a href="{{ route("widget.edit", $widget->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route("widget.delete", $widget->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>   
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $widgets-> links('pagination::bootstrap-4') }}