<?php

namespace PhuongNam\UserAndPermission\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use PhuongNam\UserAndPermission\Rules\EmailRule;

class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->has('user_id') ? $this->input('user_id') : 0;
        $rules = [
            'username' => [
                'required',
                'max:250',
                'min:6',
                Rule::unique('users')->ignore($id, 'id'),
                'regex:/^[a-zA-Z0-9_\-\.\@]+$/i',
            ],
            'name' => ['required', 'max:150'],
            'email' => ['required', new EmailRule],
            'password' => ['required', 'max:32', 'confirmed'],
            'is_active' => ['nullable'],
            'permission_ids' => ['nullable', 'array'],
            'group_ids' => ['nullable', 'array'],
        ];

        if ($id != 0) {
            unset($rules['username'], $rules['password']);
        }

        return $rules;
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

    public function message()
    {
        $id = $this->has('user_id') ? $this->input('user_id') : 0;
        $message = [
            'username.required' => __('validation.required'),
            'username.max' => __('validation.max.string', ['max' => 250]),
            'username.min' => __('validation.min.string', ['min' => 6]),
            'username.regex' => __('validation.regex'),
            'name.required' => __('validation.required'),
            'name.max' => __('validation.max.string', ['max' => 150]),
            'email.reuqired' => __('validation.required'),
            'password.required' => __('validation.required'),
            'password.max' => __('validation.max.string', ['max' => 32]),
            'password.confirmed' => __('validation.confirmed'),
            'permission_ids.array' => __('validation.array'),
            'group_ids.array' => __('validation.array'),
        ];

        if ($id != 0) {
            unset(
                $message['username.required'],
                $message['username.max'],
                $message['username.min'],
                $message['username.regex'],
                $message['password.required'],
                $message['password.max'],
                $message['password.confirmed']
            );
        }

        return $message;
    }
}
