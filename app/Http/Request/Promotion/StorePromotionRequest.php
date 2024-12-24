<?php

namespace App\Http\Request\Promotion;

use Illuminate\Foundation\Http\FormRequest;

class StorePromotionRequest extends FormRequest
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
        $rules = [
            'name' => 'required',
            'code' => 'required',
            'start_date' => 'required|custom_date_format'
        ];

        if (!$this->input('never_end_date')) {
            $rules['end_date'] = 'required|custom_date_format|custom_after:start_date';
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [
            'name.required' => 'Bạn chưa nhập tên của khuyến mại',
            'code.required' => 'Bạn chưa nhập từ khóa của khuyến mại',
            'start_date.required' => 'Bạn chưa nhập vào ngày bắt đầu khuyến mại',
            'start_date.custom_date_format' => 'Ngày bắt đầu khuyến mãi không đúng định dạng',
        ];

        if (!$this->input('never_end_date')) {
            $messages['end_date.required'] = 'Bạn chưa chọn ngày kết thúc khuyến mại';
            $messages['end_date.custom_date_format'] = 'Ngày kết thúc khuyến mãi không đúng định dạng';
            $messages['end_date.custom_after'] = 'Ngày kết thúc khuyến mãi phải lớn hơn ngày bắt đầu khuyến mãi';
        }

        return $messages;
    }
}
