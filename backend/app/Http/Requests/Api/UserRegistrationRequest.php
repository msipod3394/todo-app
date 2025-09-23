<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    // public function messages(): array
    // {
    //     return [
    //         'email.required' => 'メールアドレスは必須です。',
    //         'email.email' => '有効なメールアドレスを入力してください。',
    //         'email.unique' => 'このメールアドレスは既に使用されています。',
    //         'password.required' => 'パスワードは必須です。',
    //         'password.min' => 'パスワードは8文字以上で入力してください。',
    //         'name.max' => '名前は100文字以内で入力してください。',
    //     ];
    // }
}