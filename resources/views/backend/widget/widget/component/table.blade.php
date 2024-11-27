<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>Tên Widget</th>
        <th>Từ khóa</th>
        <th>Model</th>
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
                        {{ $widget -> keyword}}
                    </td>
                    <td>
                        {{ $widget -> model}}
                    </td>
                    <td class="text-center">
                        <a href="{{ route("user.edit", $widget->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route("user.delete", $widget->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>   
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $widgets-> links('pagination::bootstrap-4') }}