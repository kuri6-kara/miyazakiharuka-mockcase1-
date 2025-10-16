<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    /**
     * リクエストがこのバリデーションルールを適用できるか決定します。
     * 認証済みのユーザーであれば許可します。
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // 認証ミドルウェア(auth)を使用している前提でtrueを返します。
        return true;
    }

    /**
     * リクエストに適用されるバリデーションルールを取得します。
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // 商品名: 入力必須
            'name' => ['required', 'string', 'max:255'],
            // 商品説明: 入力必須, 最大255文字
            'description' => ['required', 'string', 'max:255'],
            // 商品価格: 入力必須, 整数, 100円以上 (一般的なフリマアプリの最低価格に合わせます)
            'price' => ['required', 'integer', 'min:100', 'max:9999999'],
            // 商品の状態: 選択必須
            'condition' => ['required', 'string'],
            // カテゴリ: 選択必須 (配列), 1つ以上
            'categories' => ['required', 'array', 'min:1'],
            // 商品画像: アップロード必須, 画像ファイル, 拡張子はjpeg/png, 最大2MB (2048KB)
            'item_image' => ['required', 'image', 'mimes:jpeg,png', 'max:2048'],
            // ブランド名: 任意
            'brand_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * 定義済みバリデーションルールのエラーメッセージを取得します。
     *
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
