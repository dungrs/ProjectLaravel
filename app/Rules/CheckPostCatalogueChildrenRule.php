<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\PostCatalogue;

class CheckPostCatalogueChildrenRule implements Rule
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
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
        // Giả sử hàm isNodeCheck trả về false nếu node có con
        $flag = PostCatalogue::isNodeCheck($this->id);

        // Nếu flag là false, trả về false để báo lỗi
        return $flag;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Không thể xóa do vẫn còn danh mục con.';
    }
}
