<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
		return [
			'discussion_slug' => ['uuid', 'exists:discussions,slug'],
			'content' => ['string', 'required'],
			'media' => ['array', 'max:4'],
			'media.*' => ['file', 'required', 'mimes:jpg,png,webp,mp4', 'max:20000'],
		];
	}
}
