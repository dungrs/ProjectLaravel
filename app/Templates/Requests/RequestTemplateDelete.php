<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Check{Module}ChildrenRule;

class Delete{Module}Request extends FormRequest
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
        $id = $this->route('id');
        return [
            'name' => [
                new Check{Module}ChildrenRule($id)
            ],
        ];
    }

    public function messages()
    {
        return [
            
        ];
    }

}
