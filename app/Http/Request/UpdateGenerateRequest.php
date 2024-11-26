<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGenerateRequest extends FormRequest
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
            'name' => 'required|unique:generates,name,'.$this->id.'|max:250',
            'schema' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "Bạn chưa nhập tên module.",
            'name.unique' => "Module đã tồn tại. Hãy nhập Module khác.",
            'schema.required' => "Bạn chưa nhập vào schema.",
        ];
    }
}
