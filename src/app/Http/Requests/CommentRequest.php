<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CommentRequest extends FormRequest
{
    /**
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            // 'item_id' => ['required', 'exists:items,id'],
            'comment' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     *
     * @return array
     */
    public function messages()
    {
        return [
            'comment.required' => 'コメントは必ず入力してください。',
            'comment.max' => 'コメントは255文字以内で入力してください。',
        ];
    }
}
