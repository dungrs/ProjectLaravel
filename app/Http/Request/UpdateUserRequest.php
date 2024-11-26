<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
    public function rules() {
        return [
            'email' => 'required|string|email|unique:users,email,'.$this->id.'|max:250',
            'name' => 'required|string',
            'user_catalogue_id' => 'required|integer|gt:0',
            'birthday' => 'required|date_format:Y-m-d',
        ];
    }

    public function messages() {
        return [
            'email.required' => "Bạn chưa nhập vào email.",
            'email.email' => "Email chưa đúng định dạng. Ví dụ abc@gmail.com",
            'email.unique' => "Email đã tồn tại. Hãy chọn email khác",
            'email.string' => "Email phải là dạng ký tự",
            'email.max' => "Độ dài email tối đa 250 ký tự",
            'name.required' => "Bạn chưa nhập Họ Tên",
            'name.string' => "Họ Tên phải là dạng ký tự",
            'user_catalogue_id.gt' => "Bạn chưa chọn nhóm thành viên",
            'birthday.required' => "Bạn chưa nhập ngày sinh",
            'birthday.date_format' => "Ngày sinh không đúng định dạng. Ví dụ: YYYY-MM-DD",
        ];
    }
}
