<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class TranslateRequest extends FormRequest
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
            'translate_name' => 'required',
            // Khi chỉnh sửa nó sẽ bỏ qua bản ghi hiện tại và so sánh các bản ghi khác
            'translate_canonical' => [
                'required',
                function ($attribute, $value, $fail) {
                    $option = $this->input('option'); // Lấy giá trị từ input

                    // Kiểm tra xem canonical đã tồn tại với các điều kiện cụ thể hay chưa
                    $exist = DB::table('routers')
                        ->where('canonical', $value)
                        ->where('module_id', '<>', $option['id'])
                        ->where('language_id', '<>', $option['languageId'])
                        ->exists();

                    // Nếu tồn tại, trả về lỗi
                    if ($exist) {
                        // Thông báo lỗi cụ thể
                        $fail('Đường dẫn đã tồn tại. Hãy chọn đường dẫn khác!');
                    }
                }
            ]

        ];
    }

    public function messages()
    {
        return [
            'translate_name.required' => "Bạn chưa nhập tên tiêu đề.",
            'translate_canonical.required' => "Bạn chưa nhập vào từ khóa.",
            'translate_canonical.unique' => "Từ khóa đã tồn tại hãy chọn từ khóa khác",
        ];
    }
}
