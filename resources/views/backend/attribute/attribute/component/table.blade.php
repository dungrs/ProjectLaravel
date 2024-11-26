<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th class="text-center" style="width: 50px;">
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>{{ __('messages.tableName') }}</th>
        @include('backend.dashboard.component.languageTh')
        <th style="width: 80px" class="text-center">Vị trí</th>
        <th class="text-center" style="width: 100px">{{ __('messages.tableStatus') }}</th>
        <th class="text-center" style="width: 100px">{{ __('messages.tableAction') }}</th>
    </tr>
    </thead>
    <tbody>
        @if(isset($attributes) && is_object($attributes))
            @foreach ($attributes as $attribute)
                <tr id="{{ $attribute->id }}">
                    <td class="text-center">
                        <input type="checkbox" value="{{ $attribute->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <div class="uk-flex uk-flex-middle">
                            <div class="main-info">
                                <div class="name">
                                    <span class="maintitle">{{ $attribute->name }}</span>
                                </div>
                                <div class="catalogue">
                                    <span class="text-danger">{{ __('messages.displayGroup') }}</span>
                                    @foreach ($attribute->attribute_catalogues as $val)
                                        @foreach ($val->attribute_catalogue_language as $cat)
                                            @if ($cat->language_id == $languageSelectId)
                                            <a href="{{ route('attribute.index', ['attribute_catalogue' => $val->id]) }}" title="">{{ $cat->name }}</a>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </td>
                    @include('backend.dashboard.component.languageTd', ['model' => $attribute, 'modeling' => 'Attribute'])
                    <td>
                        <input type="text" name="order" class="form-control sort-order text-right" value="{{ $attribute->order }}" data-id="{{ $attribute->id }}" data-model="{{ $config['model'] }}">
                    </td>
                    <td class="text-center" > 
                        <input value="{{ $attribute->publish }}" {{ ($attribute->publish == 2) ? 'checked' : '' }} type="checkbox" class="js-switch status js-switch-{{ $attribute->id }}" data-field="publish" data-model="{{ $config['model'] }}" data-model-id="{{ $attribute->id }}">
                    </td>
                    <td class="text-center">
                        <a href="{{ route("attribute.edit", $attribute->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route("attribute.delete", $attribute->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>   
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $attributes-> links('pagination::bootstrap-4') }}