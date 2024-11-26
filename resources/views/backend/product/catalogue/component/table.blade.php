<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th class="text-center" style="width: 50px;">
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>{{ __('messages.tableName') }}</th>
        @include('backend.dashboard.component.languageTh')
        <th class="text-center" style="width: 100px">{{ __('messages.tableStatus') }}</th>
        <th class="text-center" style="width: 100px">{{ __('messages.tableAction') }}</th>
    </tr>
    </thead>
    <tbody>
        @if(isset($productCatalogues) && is_object($productCatalogues))
            @foreach ($productCatalogues as $productCatalogue)
                <tr>
                    <td class="text-center">
                        <input type="checkbox" value="{{ $productCatalogue->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        {{ str_repeat('|----',(($productCatalogue->level > 0) ? ($productCatalogue->level -1 ): 0)).$productCatalogue->name }}
                    </td>
                    @include('backend.dashboard.component.languageTd', ['model' => $productCatalogue, 'modeling' => 'ProductCatalogue'])
                    <td class="text-center"> 
                        <input value="{{ $productCatalogue->publish }}" {{ ($productCatalogue->publish == 2) ? 'checked' : '' }} type="checkbox" class="js-switch status js-switch-{{ $productCatalogue->id }}" data-field="publish" data-model="{{ $config['model'] }}" data-model-id="{{ $productCatalogue->id }}">
                    </td>
                    <td class="text-center">
                        <a href="{{ route("product.catalogue.edit", $productCatalogue->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route("product.catalogue.delete", $productCatalogue->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>   
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $productCatalogues-> links('pagination::bootstrap-4') }}