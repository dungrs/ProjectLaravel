<?php

namespace App\Http\Request\CustomerGroup;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the customer is authorized to make this request.
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
    public function rules() {
        return [
            'email' => 'required|string|unique:customers,email,'.$this->id.'|max:250',
            'name' => 'required|string',
            'customer_catalogue_id' => 'required|integer|gt:0',
            'birthday' => 'required|date_format:Y-m-d',
        ];
    }

    public function messages() {
        return [
            'email.required' => "Bạn chưa nhập vào email.",
            'email.unique' => "Email đã tồn tại. Hãy chọn email khác.",
            'email.string' => "Email phải là dạng ký tự.",
            'email.max' => "Độ dài email tối đa 250 ký tự.",
            'name.required' => "Bạn chưa nhập Họ Tên.",
            'name.string' => "Họ Tên phải là dạng ký tự.",
            'customer_catalogue_id.gt' => "Bạn chưa chọn nhóm thành viên.",
            'birthday.required' => "Bạn chưa nhập ngày sinh.",
            'birthday.date_format' => "Ngày sinh không đúng định dạng. Ví dụ: YYYY-MM-DD.",
        ];
    }
}
