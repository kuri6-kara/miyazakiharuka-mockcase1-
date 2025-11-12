<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:100', 'max:9999999'],
            'condition' => ['required', 'string'],
            'categories' => ['required', 'array', 'min:1'],
            'item_image' => ['required', 'image', 'mimes:jpeg,png', 'max:2048'],
            'brand_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => '商品名は必須です。',
            'description.required' => '商品の説明は必須です。',
            'description.max' => '商品の説明は255文字以内で入力してください。',
            'price.required' => '販売価格は必須です。',
            'price.integer' => '販売価格は数値で入力してください。',
            'price.min' => '販売価格は100円以上で入力してください。',
            'condition.required' => '商品の状態は必須です。',
            'categories.required' => 'カテゴリは1つ以上選択してください。',
            'categories.min' => 'カテゴリは1つ以上選択してください。',
            'item_image.required' => '商品画像は必須です。',
            'item_image.image' => 'ファイルは画像である必要があります。',
            'item_image.mimes' => '画像はjpegまたはpng形式である必要があります。',
            'item_image.max' => '画像サイズは2MB以内である必要があります。',
        ];
    }
}
