<?php

namespace App\Core\Permissions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return \Gate::allows('role_create');
    }

    public function rules(): array
    {
        return [
            'name' => 'string|required|min:3',
            'permissions' => 'list',
            'permissions.*' => 'string',
            'extensions' => 'array'
        ];
    }
}
