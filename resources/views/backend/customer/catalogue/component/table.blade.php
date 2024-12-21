<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>Tên nhóm khách hàng</th>
        <th class="text-center">Số khách hàng</th>
        <th>Mô tả</th>
        <th class="text-center">Tình trạng</th>
        <th class="text-center">Thao tác</th>
    </tr>
    </thead>
    <tbody>
        @if(isset($customerCatalogues) && is_object($customerCatalogues))
            @foreach ($customerCatalogues as $customerCatalogue)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $customerCatalogue->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        {{ $customerCatalogue -> name }}
                    </td>
                    <td class="text-center">
                        {{ $customerCatalogue -> customers_count}} người
                    </td>
                    <td>
                        {{ $customerCatalogue -> description }}
                    </td>
                    <td class="text-center"> 
                        <input value="{{ $customerCatalogue->publish }}" {{ ($customerCatalogue->publish == 2) ? 'checked' : '' }} type="checkbox" class="js-switch status js-switch-{{ $customerCatalogue->id }}" data-field="publish" data-model="{{ $config['model'] }}" data-model-id="{{ $customerCatalogue->id }}">
                    </td>
                    <td class="text-center">
                        <a href="{{ route("customer.catalogue.edit", $customerCatalogue->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route("customer.catalogue.delete", $customerCatalogue->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>   
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $customerCatalogues-> links('pagination::bootstrap-4') }}