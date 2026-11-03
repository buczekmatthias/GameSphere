<?php

namespace App\Http\Requests\Game;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
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
            'title' => ['nullable', 'string', 'max:100'],
            'genre' => ['nullable', 'string', 'max:100'],
            'released_after' => ['nullable', 'date_format:Y-m-d'],
            'released_before' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:released_after'],
        ];
    }
}
