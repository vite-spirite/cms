<?php

namespace App\Core\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return \Gate::allows('user_edit');
    }


    public function rules(): array
    {
        return [
            'id' => 'required|exists:users,id',
            'email' => 'required|email',
            'name' => 'required',
            'password' => 'nullable|confirmed|min:8',
            'extensions' => 'sometimes|array',
        ];
    }
}
