<?php

namespace App\Rules\Promotion;

use Illuminate\Contracts\Validation\Rule;

class ProductAndQuantityRule implements Rule
{
    protected $data;
    protected $errorMessage;

    public function __construct($data)
    {
        $this->data = $data;
        $this->errorMessage = 'Invalid promotion configuration.';
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Kiểm tra nếu không có giá trị số lượng tối thiểu
        if (empty($this->data['product_and_quantity']['quantity']) || 
            normalizeAmount($this->data['product_and_quantity']['quantity']) <= 0) {
            $this->errorMessage = 'Bạn phải nhập số lượng mua tối thiểu để được hưởng chiết khấu.';
            return false;
        }

        // Kiểm tra nếu giá trị chiết khấu không hợp lệ
        if (empty($this->data['product_and_quantity']['discountValue']) || 
            normalizeAmount($this->data['product_and_quantity']['discountValue']) <= 0) {
            $this->errorMessage = 'Bạn phải nhập vào giá trị của chiết khấu.';
            return false;
        }

        // Kiểm tra nếu không có đối tượng áp dụng
        if (empty($this->data['object']['name'])) {
            $this->errorMessage = 'Bạn chưa chọn đối tượng áp dụng chiết khấu.';
            return false;
        }

        return true;
    }

    /**
     * Chuẩn hóa giá trị số tiền từ định dạng "100,000" thành số nguyên.
     *
     * @param string|null $amount
     * @return int
     */
    protected function normalizeAmount($amount)
    {
        return (int) str_replace(',', '', $amount ?? 0);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage;
    }
}
