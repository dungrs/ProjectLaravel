<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSlideRequest extends FormRequest
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
            'keyword' => 'required|unique:slides,keyword,'.$this->id.'|max:250',
            'slide.image' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Bạn chưa nhập tên của Slide',
            'keyword.required' => 'Bạn chưa nhập từ khóa Slide',
            'slide.image.required' => 'Bạn chưa chọn hình ảnh nào cho Slide',
            'keyword.unique' => "Keyword đã tồn tại. Hãy chọn keyword khác",
        ];
    }
}
