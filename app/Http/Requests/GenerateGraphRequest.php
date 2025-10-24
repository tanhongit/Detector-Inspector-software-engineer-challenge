<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateGraphRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => [
                'required',
                'url',
                'regex:/^https?:\/\/(en\.)?wikipedia\.org\/.+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'url.required' => 'Please enter a Wikipedia URL.',
            'url.url' => 'Please enter a valid URL.',
            'url.regex' => 'Please enter a valid Wikipedia URL (e.g., https://en.wikipedia.org/wiki/...)',
        ];
    }
}
