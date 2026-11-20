<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class SortableListRequest extends FormRequest
{
    /** @return list<string> */
    abstract protected function sortableColumns(): array;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isStaff();
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('column'))) {
            $this->merge(['column' => strtolower(trim($this->input('column')))]);
        }
        if (is_string($this->input('order'))) {
            $this->merge(['order' => strtolower(trim($this->input('order')))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'column' => ['nullable', 'string', Rule::in($this->sortableColumns())],
            'order'  => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ];
    }
}
