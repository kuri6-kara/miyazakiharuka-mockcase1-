<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

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
        $rules = [
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
        ];

        if (!Session::has('shipping_address')) {
            $user = Auth::user();

            if (empty($user->postcode) || empty($user->address)) {
                $rules['shipping_address_set'] = ['required'];
            }
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
            'shipping_address_set.required' => '配送先情報が設定されていません。プロフィールに住所が登録されていない場合は、一度「変更する」画面から住所を設定してください。',
        ];
    }
}
