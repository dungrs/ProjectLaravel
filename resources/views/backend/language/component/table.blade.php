<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th style="width: 100px;">Ảnh</th>
        <th>Tên Ngôn Ngữ</th>
        <th>Canoncial</th>
        <th>Mô tả</th>
        <th class="text-center">Tình trạng</th>
        <th class="text-center">Thao tác</th>
    </tr>
    </thead>
    <tbody>
        @if(isset($languagesList) && is_object($languages))
            @foreach ($languagesList as $language)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $language->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <span class="image img-cover">
                            <img src="{{ $language->image }}" alt="">
                        </span>
                    </td>
                    <td>
                        {{ $language -> name }}
                    </td>
                    <td>
                        {{ $language -> canonical}}
                    </td>
                    <td>
                        {{ $language -> description }}
                    </td>
                    <td class="text-center"> 
                        <input value="{{ $language->publish }}" {{ ($language->publish == 2) ? 'checked' : '' }} type="checkbox" class="js-switch status js-switch-{{ $language->id }}" data-field="publish" data-model="{{ $config['model'] }}" data-model-id="{{ $language->id }}">
                    </td>
                    <td class="text-center">
                        <a href="{{ route("language.edit", $language->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route("language.delete", $language->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>   
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $languagesList-> links('pagination::bootstrap-4') }}
