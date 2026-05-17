<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
           // 商品名：入力必須
            'name' => ['required', 'string'],

            // 商品説明：入力必須、最大文字数255
            'description' => ['required', 'string', 'max:255'],

            // 商品画像：アップロード必須、拡張子が .jpeg もしくは .png
            // ⚠️ Laravelの 'image' はjpgやpngを含みますが、拡張子を明確に限定するため 'mimes:jpeg,png' を指定します
            'item_image' => ['required', 'file', 'mimes:jpeg,png'],

            // 商品のカテゴリー：選択必須
            'categories' => ['required', 'array', 'min:1'],

            // 商品の状態：選択必須
            'condition' => ['required', 'string'],

            // 商品価格：入力必須、数値型、0円以上
            'price' => ['required', 'integer', 'min:0'], 
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '商品名を入力してください',
            
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',
            
            'item_image.required' => '商品画像を選択してください',
            'item_image.mimes' => '商品画像は .jpeg もしくは .png 形式でアップロードしてください',
            
            'categories.required' => '商品のカテゴリーを選択してください',
            
            'condition.required' => '商品の状態を選択してください',
            
            'price.required' => '商品価格を入力してください',
            'price.integer' => '商品価格は数値で入力してください',
            'price.min' => '商品価格は0円以上で指定してください',
        ];
    } 
}
