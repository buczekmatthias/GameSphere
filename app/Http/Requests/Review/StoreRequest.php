<?php

namespace App\Http\Requests\Review;

use App\Enum\ReviewKey;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $keys = array_column(ReviewKey::cases(), 'value');

        return [
            'content' => ['string', 'required'],
            'game_slug' => ['required', 'uuid', 'exists:games,slug'],
            'ratings' => ['required', Rule::array($keys), 'required_array_keys:'.implode(",", $keys)],
            'ratings.*' => ['numeric', 'required', 'between:1,5'],
        ];
    }
}
