<?php

namespace PhuongNam\UserAndPermission\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => ['required', 'max:250'],
            'password' => ['required', 'max:32'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'username.required' => __('validation.required'),
            'username.max' => __('validation.max', ['max' => 250]),
            'password.required' => __('validation.required'),
            'password.max' => __('validation.max', ['max' => 32]),
        ];
    }
}
