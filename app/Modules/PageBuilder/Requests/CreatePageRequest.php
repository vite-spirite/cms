<?php

namespace App\Modules\PageBuilder\Requests;

use App\Rules\ValidBlockContent;
use Illuminate\Foundation\Http\FormRequest;

class CreatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'slug' => 'string|nullable',
            'status' => 'required|string|in:draft,published,archived',
            'og_balises' => 'array',
            'og_balises.*' => 'string|nullable',
            'content' => ['array', new ValidBlockContent],
        ];
    }
}
