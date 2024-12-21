<?php

namespace App\Http\Request\CustomerGroup;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the customer is authorized to make this request.
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
            'email' => 'required|string|unique:customers|max:250',
            'name' => 'required|string',
            'customer_catalogue_id' => 'required|integer|gt:0',
            'password' => 'required|string|min:6',
            're_password' => 'required|string|same:password',
            'birthday' => 'required|date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => "Bạn chưa nhập vào email.",
            'email.unique' => "Email đã tồn tại. Hãy chọn email khác.",
            'email.string' => "Email phải là dạng ký tự.",
            'email.max' => "Độ dài email tối đa 250 ký tự.",
            'name.required' => "Bạn chưa nhập Họ Tên.",
            'name.string' => "Họ Tên phải là dạng ký tự.",
            'customer_catalogue_id.required' => "Bạn chưa chọn nhóm thành viên.",
            'customer_catalogue_id.gt' => "Nhóm thành viên phải là giá trị lớn hơn 0.",
            'birthday.required' => "Bạn chưa nhập ngày sinh.",
            'birthday.date_format' => "Ngày sinh không đúng định dạng. Ví dụ: YYYY-MM-DD.",
            'password.required' => "Bạn chưa nhập vào mật khẩu.",
            'password.min' => "Độ dài mật khẩu tối thiểu là 6 ký tự.",
            're_password.required' => "Bạn chưa nhập vào ô Nhập lại mật khẩu.",
            're_password.same' => "Mật khẩu không khớp.",
        ];
    }
}