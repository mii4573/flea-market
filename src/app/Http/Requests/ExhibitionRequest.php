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
          'item_image'  => 'required|image|mimes:jpeg,png',
            'categories'  => 'required|array',
            'condition'   => 'required|string',
            'name'        => 'required|string|max:255',
            'brand_name'  => 'nullable|string|max:255',
            'description' => 'required|string|max:255',
            'price'       => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            // 商品画像
            'item_image.required'  => '商品画像のアップロードは必須です。',
            'item_image.image'     => 'アップロードされたファイルは画像ではありません。',
            'item_image.mimes'     => '商品画像の拡張子は.jpegもしくは.png形式です。',

            // カテゴリー
            'categories.required'  => '商品のカテゴリー選択は必須です。',

            // 商品の状態
            'condition.required'   => '商品の状態選択は必須です。',

            // 商品名
            'name.required'        => '商品名を入力してください。',

            // 商品の説明
            'description.required' => '商品説明を入力してください。',
            'description.max'      => '商品説明は255文字以内で入力してください。',

            // 販売価格
            'price.required'       => '商品価格を入力してください。',
            'price.integer'        => '商品価格は数値型で入力してください。',
            'price.min'            => '商品価格は0円以上で入力してください。',
        ];
    } 
}
