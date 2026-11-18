<?php

namespace App\Http\Requests\Discussion;

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
        $table = match ($this->input('type')) {
            'games'  => 'games',
            'genres' => 'genres',
            default  => null,
        };

        return [
            'title' => ['string', 'required'],
            'type' => ['string', 'in:games,genres'],
            'slug' => ['uuid',  'required', Rule::when($table !== null, [Rule::exists($table, 'slug')])],
            'content' => ['string', 'required'],
            'media' => ['array', 'max:'.config('globals.form.files.media.max_files')],
            'media.*' => ['file', 'mimetypes:'.config('globals.form.files.media.accept_type')]
        ];
    }
}
