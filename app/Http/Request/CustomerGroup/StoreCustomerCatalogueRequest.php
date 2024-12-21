<?php

namespace App\Http\Request\CustomerGroup;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerCatalogueRequest extends FormRequest
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
            'name' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "Bạn chưa nhập Nhóm thành viên",
            'name.string' => "Nhóm thành viên phải là dạng ký tự",
        ];
    }
}
