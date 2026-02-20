<?php


namespace App\Core\Permissions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return \Gate::allows('role_update');
    }

    public function rules(): array
    {
        return [
            'id' => 'bail|required|integer|exists:roles,id',
            'name' => 'string|required|min:3',
            'permissions' => 'list',
            'permissions.*' => 'string',
            'extensions' => 'array|sometimes'
        ];
    }
}
