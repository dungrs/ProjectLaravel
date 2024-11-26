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
        @if(isset($products) && is_object($products))
            @foreach ($products as $product)
                <tr id="{{ $product->id }}">
                    <td class="text-center">
                        <input type="checkbox" value="{{ $product->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <div class="uk-flex uk-flex-middle">
                            <div class="image mr5">
                                <div class="img-cover image-post">
                                    <img src="{{ $product->image }}" alt="">
                                </div>
                            </div>
                            <div class="main-info">
                                <div class="name">
                                    <span class="maintitle">{{ $product->name }}</span>
                                </div>
                                <div class="catalogue">
                                    <span class="text-danger">{{ __('messages.displayGroup') }}</span>
                                    @foreach ($product->product_catalogues as $val)
                                        @foreach ($val->product_catalogue_language as $cat)
                                            @if ($cat->language_id == $languageSelectId)
                                            <a href="{{ route('product.index', ['product_catalogue' => $val->id]) }}" title="">{{ $cat->name }}</a>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </td>
                    @include('backend.dashboard.component.languageTd', ['model' => $product, 'modeling' => 'Product'])
                    <td>
                        <input type="text" name="order" class="form-control sort-order text-right" value="{{ $product->order }}" data-id="{{ $product->id }}" data-model="{{ $config['model'] }}">
                    </td>
                    <td class="text-center" > 
                        <input value="{{ $product->publish }}" {{ ($product->publish == 2) ? 'checked' : '' }} type="checkbox" class="js-switch status js-switch-{{ $product->id }}" data-field="publish" data-model="{{ $config['model'] }}" data-model-id="{{ $product->id }}">
                    </td>
                    <td class="text-center">
                        <a href="{{ route("product.edit", $product->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route("product.delete", $product->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>   
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $products-> links('pagination::bootstrap-4') }}