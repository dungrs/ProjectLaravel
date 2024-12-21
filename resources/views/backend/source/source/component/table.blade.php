<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>Tên Nguồn Khách</th>
        <th>Từ khóa</th>
        <th>Mô tả</th>
        <th class="text-center">Tình trạng</th>
        <th class="text-center">Thao tác</th>
    </tr>
    </thead>
    <tbody>
        @if(isset($sources) && is_object($sources))
            @foreach ($sources as $source)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $source->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        {{ $source -> name }}
                    </td>
                    <td>
                        {{ $source -> keyword }}
                    </td>
                    <td>
                        {{ strip_tags(html_entity_decode($source->description)) }}
                    </td>
                    <td class="text-center">
                        <input value="{{ $source->publish }}" {{ ($source->publish == 2) ? 'checked' : '' }} type="checkbox" class="js-switch status js-switch-{{ $source->id }}" data-field="publish" data-model="{{ $config['model'] }}" data-model-id="{{ $source->id }}">
                    </td>
                    <td class="text-center">
                        <a href="{{ route("source.edit", $source->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route("source.delete", $source->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>   
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $sources-> links('pagination::bootstrap-4') }}