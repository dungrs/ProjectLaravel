<?php

namespace App\Rules\Promotion;

use Illuminate\Contracts\Validation\Rule;

class OrderAmountRangeRule implements Rule
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
        // Kiểm tra nếu không có giá trị nào được thiết lập
        if (
            !isset($this->data['amountFrom']) ||
            !isset($this->data['amountTo']) ||
            !isset($this->data['amountValue']) ||
            count($this->data['amountFrom']) == 0 ||
            $this->data['amountFrom'][0] == ''
        ) {
            $this->errorMessage = 'Bạn phải khởi tạo giá trị cho khoảng khuyến mãi.';
            return false;
        }

        // Kiểm tra nếu có giá trị không hợp lệ trong `amountValue`
        if (in_array(0, $this->data['amountValue']) || in_array('', $this->data['amountValue'])) {
            $this->errorMessage = 'Cấu hình giá trị khuyến mại không hợp lệ.';
            return false;
        }

        // Kiểm tra nếu có xung đột giữa các khoảng giá trị
        for ($i = 0; $i < count($this->data['amountFrom']); $i++) {
            $amount_from_1 = normalizeAmount($this->data['amountFrom'][$i]);
            $amount_to_1 = normalizeAmount($this->data['amountTo'][$i]);

            for ($j = $i + 1; $j < count($this->data['amountTo']); $j++) {
                $amount_from_2 = normalizeAmount($this->data['amountFrom'][$j]);
                $amount_to_2 = normalizeAmount($this->data['amountTo'][$j]);

                if ($amount_from_1 <= $amount_to_2 && $amount_to_1 >= $amount_from_2) {
                    $this->errorMessage = 'Có xung đột giữa các khoảng giá trị khuyến mại! Hãy kiểm tra lại dữ liệu';
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Chuẩn hóa giá trị số tiền từ định dạng "100,000" thành số nguyên.
     *
     * @param string $amount
     * @return int
     */

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
