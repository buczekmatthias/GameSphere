<?php

namespace App\Http\Requests\Review;

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
			'content' => ['string', 'required'],
			'game_slug' => ['required', 'uuid', 'exists:games,slug'],
			'ratings' => ['array', 'required'],
			'ratings.*' => ['numeric', 'required', 'between:1,5'],
		];
	}
}
