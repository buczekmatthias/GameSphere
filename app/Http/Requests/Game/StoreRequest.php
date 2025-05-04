<?php

namespace App\Http\Requests\Game;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return $this->user()->canAddGame();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'title' => ['string', 'required'],
			'description' => ['string', 'required', 'min:50'],
			'thumbnail' => ['image', 'required'],
			'media' => ['array'],
			'media.*' => ['file', 'required', 'mimes:jpg,png,webp,mp4'],
			'released_at' => ['date', 'required'],
			'genre' => ['uuid', 'exists:genres,slug', 'required'],
		];
	}
}
