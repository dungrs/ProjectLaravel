<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductCatalogueRequest extends FormRequest
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
            'canonical' => 'required|unique:routers'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "Bạn chưa nhập vào ô tiêu đề.",
            'canonical.required' => "Bạn chưa nhập vào đường dẫn.",
            'canonical.unique' => "Đường dẫn đã tồn tại, Hãy chọn đường dẫn khác.",
        ];
    }
}
