<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>Họ Tên</th>
        <th>Email</th>
        <th>Số điện thoại</th>
        <th>Địa chỉ</th>
        <th class="text-center">Nhóm Khách Hàng</th>
        <th class="text-center">Nguồn</th>
        <th class="text-center">Tình trạng</th>
        <th class="text-center">Thao tác</th>
    </tr>
    </thead>
    <tbody>
        @if(isset($customers) && is_object($customers))
            @foreach ($customers as $customer)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $customer->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        {{ $customer -> name }}
                    </td>
                    <td>
                        {{ $customer -> email}}
                    </td>
                    <td>
                        {{ $customer -> phone}}
                    </td>
                    <td>
                        {{ $customer -> address}}
                    </td>
                    <td class="text-center">
                        {{ $customer -> customer_catalogues -> name}}
                    </td>
                    <td class="text-center">
                        {{ $customer -> sources -> name}}
                    </td>
                    <td class="text-center"> 
                        <input {{ ($customer->catalogue_publish == 1) ? 'disabled' : '' }} value="{{ $customer->publish }}" {{ ($customer->publish == 2) ? 'checked' : '' }} type="checkbox" class="js-switch status js-switch-{{ $customer->id }}" data-field="publish" data-model="{{ $config['model'] }}" data-model-id="{{ $customer->id }}">
                    </td>
                    <td class="text-center">
                        <a href="{{ route("customer.edit", $customer->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route("customer.delete", $customer->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>   
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $customers-> links('pagination::bootstrap-4') }}