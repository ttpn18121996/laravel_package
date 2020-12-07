<?php

namespace PhuongNam\UserAndPermission\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GroupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->has('group_id') ? $this->input('group_id') : 0;

        return [
            'name' => [
                'required',
                'max:250',
                Rule::unique('groups')->ignore($id, 'id'),
            ],
            'description' => ['nullable', 'max:255'],
            'permission_ids' => ['nullable', 'array'],
            'user_ids' => ['nullable', 'array'],
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

    public function message()
    {
        $message = [
            'name.required' => __('validation.required'),
            'name.max' => __('validation.max.string', ['max' => 250]),
            'description.max' => __('validation.max.string', ['max' => 255]),
            'permission_ids.array' => __('validation.array'),
            'user_ids.array' => __('validation.array'),
        ];

        return $message;
    }
}
