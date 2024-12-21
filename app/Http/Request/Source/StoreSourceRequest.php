<?php

namespace App\Http\Request\Source;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSourceRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name' => 'required|string|max:255', // Sử dụng quy tắc string thay cho keyword
            'keyword' => 'required|unique:sources,keyword|string|max:255', // Quy tắc khác
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Bạn chưa nhập tên của nguồn khách',
            'keyword.required' => 'Bạn chưa nhập từ khóa của nguồn khách',
            'keyword.unique' => 'Từ khóa đã tồn tại, hãy chọn từ khóa khác',
        ];
    }
}