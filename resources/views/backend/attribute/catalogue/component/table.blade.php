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
        @if(isset($attributeCatalogues) && is_object($attributeCatalogues))
            @foreach ($attributeCatalogues as $attributeCatalogue)
                <tr>
                    <td class="text-center">
                        <input type="checkbox" value="{{ $attributeCatalogue->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        {{ str_repeat('|----',(($attributeCatalogue->level > 0) ? ($attributeCatalogue->level -1 ): 0)).$attributeCatalogue->name }}
                    </td>
                    @include('backend.dashboard.component.languageTd', ['model' => $attributeCatalogue, 'modeling' => 'AttributeCatalogue'])
                    <td class="text-center"> 
                        <input value="{{ $attributeCatalogue->publish }}" {{ ($attributeCatalogue->publish == 2) ? 'checked' : '' }} type="checkbox" class="js-switch status js-switch-{{ $attributeCatalogue->id }}" data-field="publish" data-model="{{ $config['model'] }}" data-model-id="{{ $attributeCatalogue->id }}">
                    </td>
                    <td class="text-center">
                        <a href="{{ route("attribute.catalogue.edit", $attributeCatalogue->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route("attribute.catalogue.delete", $attributeCatalogue->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>   
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $attributeCatalogues-> links('pagination::bootstrap-4') }}