<?php

namespace App\Http\Requests\User;

use App\Enum\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserListFilterRequest extends FormRequest
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
            'contains' => ['string', 'sometimes'],
            'role' => [
                'sometimes',
                'string',
                Rule::in(
                    array_column(
                        UserRole::cases(),
                        'value'
                    )
                )
            ]
        ];
    }
}
