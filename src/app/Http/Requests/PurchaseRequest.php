<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;

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
        // 支払い方法IDは必須
        $rules = [
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
        ];

        // 配送先情報がセッションに存在するかをチェック
        // 配送先情報はセッションから取得するため、フォームには現れないが、
        // 配送先が設定されていることを確認するためにチェックを行う。
        if (!Session::has('shipping_address')) {
            $rules['shipping_address_set'] = ['required'];
        }

        return $rules;
    }

    /**
     * Define custom messages for validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'payment_method_id.required' => '支払い方法を選択してください。',
            'payment_method_id.exists' => '無効な支払い方法が選択されました。',
            'shipping_address_set.required' => '配送先情報が設定されていません。一度変更画面から住所を設定してください。',
        ];
    }
}