<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'postcode' => 'required|string|max:8',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'profile_image.image' => 'プロフィール画像は画像ファイル形式でアップロードしてください。',
            'profile_image.mimes' => 'プロフィール画像としてアップロードできるファイル形式は、JPEG, PNG のいずれかです。',
            'profile_image.max' => 'プロフィール画像としてアップロードできるファイルのサイズは2MBまでです。',

            'name.required' => 'ユーザー名は必ず入力してください。',
            'name.string' => 'ユーザー名は文字列で入力してください。',
            'name.max' => 'ユーザー名は255文字以内で入力してください。',

            'postcode.required' => '郵便番号は必ず入力してください。',
            'postcode.string' => '郵便番号は文字列で入力してください。',
            'postcode.max' => '郵便番号は8文字以内で入力してください。',

            'address.required' => '住所は必ず入力してください。',
            'address.string' => '住所は文字列で入力してください。',
            'address.max' => '住所は255文字以内で入力してください。',

            'building.string' => '建物名は文字列で入力してください。',
            'building.max' => '建物名は255文字以内で入力してください。',
        ];
    }
}