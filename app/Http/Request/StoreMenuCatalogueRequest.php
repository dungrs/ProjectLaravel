<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuCatalogueRequest extends FormRequest
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
            'keyword' => 'required|unique:menu_catalogues',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "Bạn chưa nhập vào tên nhóm menu.",
            'keyword.unique' => "Nhóm menu đã tồn tại.",
            'keyword.required' => "Bạn chưa nhập từ khóa của menu.",
        ];
    }
}
