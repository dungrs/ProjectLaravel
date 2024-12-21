<?php

namespace App\Http\Request\Source;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSourceRequest extends FormRequest
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
            'name' => 'required|string|max:255', // Đảm bảo "name" là chuỗi và có giới hạn ký tự
            'keyword' => 'required|string|max:250|unique:sources,keyword,' . $this->route('source'), 
            // Kiểm tra duy nhất dựa trên ID từ route hoặc từ `$this->id`
        ];
    }

    /**
     * Custom error messages for validation.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'name.required' => 'Bạn chưa nhập tên nguồn khách.',
            'name.string' => 'Tên nguồn khách phải là chuỗi ký tự.',
            'name.max' => 'Tên nguồn khách không được vượt quá 255 ký tự.',
            'keyword.required' => 'Bạn chưa nhập từ khóa nguồn khách.',
            'keyword.string' => 'Từ khóa nguồn khách phải là chuỗi ký tự.',
            'keyword.max' => 'Từ khóa nguồn khách không được vượt quá 250 ký tự.',
            'keyword.unique' => 'Từ khóa đã tồn tại, hãy chọn từ khóa khác.',
        ];
    }
}
