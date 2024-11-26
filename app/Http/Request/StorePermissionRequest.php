<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    // Xác định người dùng được phép thực hiện yêu cầu này hay không
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'canonical' => 'required|unique:permission'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "Bạn chưa nhập tên quyền.",
            'canonical.required' => "Bạn chưa nhập vào từ khóa quyền.",
            'canonical.unique' => "Từ khóa đã tồn tại hãy chọn từ khóa khác",
        ];
    }
}
