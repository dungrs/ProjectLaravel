<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>Tên nhóm thành viên</th>
        <th class="text-center">Số thành viên</th>
        <th>Mô tả</th>
        <th class="text-center">Tình trạng</th>
        <th class="text-center">Thao tác</th>
    </tr>
    </thead>
    <tbody>
        @if(isset($userCatalogues) && is_object($userCatalogues))
            @foreach ($userCatalogues as $userCatalogue)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $userCatalogue->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        {{ $userCatalogue -> name }}
                    </td>
                    <td class="text-center">
                        {{ $userCatalogue -> users_count}} người
                    </td>
                    <td>
                        {{ $userCatalogue -> description }}
                    </td>
                    <td class="text-center"> 
                        <input value="{{ $userCatalogue->publish }}" {{ ($userCatalogue->publish == 2) ? 'checked' : '' }} type="checkbox" class="js-switch status js-switch-{{ $userCatalogue->id }}" data-field="publish" data-model="{{ $config['model'] }}" data-model-id="{{ $userCatalogue->id }}">
                    </td>
                    <td class="text-center">
                        <a href="{{ route("user.catalogue.edit", $userCatalogue->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route("user.catalogue.delete", $userCatalogue->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>   
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $userCatalogues-> links('pagination::bootstrap-4') }}