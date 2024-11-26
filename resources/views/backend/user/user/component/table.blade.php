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
        <th class="text-center">Nhóm thành viên</th>
        <th class="text-center">Tình trạng</th>
        <th class="text-center">Thao tác</th>
    </tr>
    </thead>
    <tbody>
        @if(isset($users) && is_object($users))
            @foreach ($users as $user)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $user->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        {{ $user -> name }}
                    </td>
                    <td>
                        {{ $user -> email}}
                    </td>
                    <td>
                        {{ $user -> phone}}
                    </td>
                    <td>
                        {{ $user -> address}}
                    </td>
                    <td class="text-center">
                        {{ $user -> user_catalogues -> name}}
                    </td>
                    <td class="text-center"> 
                        <input {{ ($user->catalogue_publish == 1) ? 'disabled' : '' }} value="{{ $user->publish }}" {{ ($user->publish == 2) ? 'checked' : '' }} type="checkbox" class="js-switch status js-switch-{{ $user->id }}" data-field="publish" data-model="{{ $config['model'] }}" data-model-id="{{ $user->id }}">
                    </td>
                    <td class="text-center">
                        <a href="{{ route("user.edit", $user->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route("user.delete", $user->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>   
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $users-> links('pagination::bootstrap-4') }}