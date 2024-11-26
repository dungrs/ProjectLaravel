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
        @if(isset(${module}s) && is_object(${module}s))
            @foreach (${module}s as ${module})
                <tr id="{{ ${module}->id }}">
                    <td class="text-center">
                        <input type="checkbox" value="{{ ${module}->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <div class="uk-flex uk-flex-middle">
                            <div class="image mr5">
                                <div class="img-cover image-post">
                                    <img src="{{ ${module}->image }}" alt="">
                                </div>
                            </div>
                            <div class="main-info">
                                <div class="name">
                                    <span class="maintitle">{{ ${module}->name }}</span>
                                </div>
                                <div class="catalogue">
                                    <span class="text-danger">{{ __('messages.displayGroup') }}</span>
                                    @foreach (${module}->{module}_catalogues as $val)
                                        @foreach ($val->{module}_catalogue_language as $cat)
                                            @if ($cat->language_id == $languageSelectId)
                                            <a href="{{ route('{module}.index', ['{module}_catalogue' => $val->id]) }}" title="">{{ $cat->name }}</a>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </td>
                    @include('backend.dashboard.component.languageTd', ['model' => ${module}, 'modeling' => '{Module}'])
                    <td>
                        <input type="text" name="order" class="form-control sort-order text-right" value="{{ ${module}->order }}" data-id="{{ ${module}->id }}" data-model="{{ $config['model'] }}">
                    </td>
                    <td class="text-center" > 
                        <input value="{{ ${module}->publish }}" {{ (${module}->publish == 2) ? 'checked' : '' }} type="checkbox" class="js-switch status js-switch-{{ ${module}->id }}" data-field="publish" data-model="{{ $config['model'] }}" data-model-id="{{ ${module}->id }}">
                    </td>
                    <td class="text-center">
                        <a href="{{ route("{module}.edit", ${module}->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route("{module}.delete", ${module}->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>   
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ ${module}s-> links('pagination::bootstrap-4') }}