<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'payment_method' => 'required|string',
            'address_id'     => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
           'payment_method.required' => '支払い方法選択必須',
           'address_id.required'     => '配送先選択必須',
        ]; 
    }
}
