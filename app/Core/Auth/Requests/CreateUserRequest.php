<?php

namespace App\Core\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return \Gate::allows('user_create');
    }

    public function rules(): array
    {
        return [
            'email' => 'email|required|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'name' => 'required|string|min:3',
            'extensions' => 'sometimes|array'
        ];
    }
}
