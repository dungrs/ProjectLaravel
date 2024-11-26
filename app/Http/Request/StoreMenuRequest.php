<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMenuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    // Để lấy các quy tắc xác thực
    public function rules()
    {
        return [
            'menu_catalogue_id' => 'gt:0',
            'menu.name' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'menu_catalogue_id.gt' => 'Bạn chưa chọn vị trí hiển thị Menu',
            'menu.name.required' => 'Bạn phải tạo ít nhất 1 menu'
        ];
    }
}
