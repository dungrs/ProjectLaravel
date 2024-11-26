<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>Tên nhóm</th>
        <th>Từ khóa</th>
        <th class="text-center">Tình trạng</th>
        <th class="text-center">Thao tác</th>
    </tr>
    </thead>
    <tbody>
        @if(isset($slides) && is_object($slides))
            @foreach ($slides as $slide)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $slide->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        {{ $slide -> name }}
                    </td>
                    <td>
                        {{ $slide -> keyword}}
                    </td>
                    <td class="text-center">
                        <input value="{{ $slide->publish }}" {{ ($slide->publish == 2) ? 'checked' : '' }} type="checkbox" class="js-switch status js-switch-{{ $slide->id }}" data-field="publish" data-model="{{ $config['model'] }}" data-model-id="{{ $slide->id }}">
                    </td>
                    <td class="text-center">
                        <a href="{{ route("slide.edit", $slide->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route("slide.delete", $slide->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>   
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $slides-> links('pagination::bootstrap-4') }}