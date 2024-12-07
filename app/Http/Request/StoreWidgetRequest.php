<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class StoreWidgetRequest extends FormRequest
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
            'name' => 'required',
            'keyword' => "required|unique:widgets",
            'short_code' => "required|unique:widgets",
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "Bạn chưa nhập tên của Widget",
            'keyword.required' => "Bạn chưa nhập từ khóa của Widget",
            'keyword.unique' => "Từ khóa đã tổn tại, hãy chọn từ khóa khác",
            'short_code.unique' => "Shortcode đã tổn tại, hãy chọn tên shortcode khác",
        ];
    }
}
