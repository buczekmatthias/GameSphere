<?php

namespace App\Http\Requests\Discussion;

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
			'title' => ['string', 'required'],
			'slug' => ['uuid', 'required', $this->post('type') === 'game' ? 'exists:games,slug' : 'exists:genres,slug'],
			'type' => ['string', 'in:game,genre']
		];
	}
}
