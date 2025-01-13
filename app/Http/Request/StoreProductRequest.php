<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'canonical' => 'required|string|unique:routers|max:255',
            'attribute' => 'required|array', // Kiểm tra nếu attribute là mảng
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'name.required' => 'Bạn chưa nhập vào ô tiêu đề.',
            'canonical.required' => 'Bạn chưa nhập vào đường dẫn.',
            'canonical.unique' => 'Đường dẫn đã tồn tại, hãy chọn đường dẫn khác.',
            'attribute.required' => 'Bạn chưa nhập phiên bản cho sản phẩm.',
        ];
    }

    /**
     * Customize the validation data to handle null attributes.
     *
     * @return array
     */
    public function validationData()
    {
        $data = $this->all();

        // Kiểm tra nếu attribute null, gán giá trị mặc định
        if (empty($data['attribute'])) {
            $data['attribute'] = []; // Gán giá trị mặc định cho attribute
        }

        return $data;
    }
}