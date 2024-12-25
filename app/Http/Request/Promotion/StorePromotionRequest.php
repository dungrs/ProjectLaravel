<?php

namespace App\Http\Request\Promotion;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Promotion\OrderAmountRangeRule;
use App\Rules\Promotion\ProductAndQuantityRule;

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
            'start_date' => 'required|custom_date_format',
            'method' => 'required|in:' . implode(',', array_keys(__('module.promotion')))
        ];

        if (!$this->input('never_end_date')) {
            $rules['end_date'] = 'required|custom_date_format|custom_after:start_date';
        }

        $method = $this->input('method');
        switch ($method) {
            case 'order_amount_range':
                $rules['method'] = [new OrderAmountRangeRule($this->input('promotion_order_amount_range'))];
                break;
            case 'product_and_quantity';
                $rules['method'] = [new ProductAndQuantityRule($this->only('product_and_quantity', 'object'))];
                break;
            default:
                break;
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
            'method.required' => 'Bạn chưa chọn hình thức khuyến mãi',
            'method.in' => 'Hình thức khuyến mãi không hợp lệ',
            // 'method_module.required' => 'Bạn chưa nhập thông tin cho hình thức khuyến mãi "order_amount_range"',
        ];

        if (!$this->input('never_end_date')) {
            $messages['end_date.required'] = 'Bạn chưa chọn ngày kết thúc khuyến mại';
            $messages['end_date.custom_date_format'] = 'Ngày kết thúc khuyến mãi không đúng định dạng';
            $messages['end_date.custom_after'] = 'Ngày kết thúc khuyến mãi phải lớn hơn ngày bắt đầu khuyến mãi';
        }

        return $messages;
    }
}
