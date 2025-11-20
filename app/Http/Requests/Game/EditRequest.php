<?php

namespace App\Http\Requests\Game;

use Illuminate\Foundation\Http\FormRequest;

class EditRequest extends FormRequest
{
	const TOTAL_AVAILABLE_MEDIA_SLOTS = 6;

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
		$game = $this->route('game');
		$sizeOfExistingMedia = sizeof($game->media ?? []);
		$sizeOfMediaToDelete = sizeof($this->post('media_to_delete', []));

		$availableMediaSlots = self::TOTAL_AVAILABLE_MEDIA_SLOTS - $sizeOfExistingMedia + $sizeOfMediaToDelete;

		return [
			'title' => ['string', 'required'],
			'description' => ['string', 'required', 'min:50'],
			'thumbnail' => ['image', 'nullable'],
			'media' => ['array', 'max:'.$availableMediaSlots],
			'media.*' => ['file', 'required', 'mimes:jpg,png,webp,mp4', 'max:20000'],
			'released_at' => ['date', 'nullable'],
			'genre' => ['uuid', 'exists:genres,slug', 'required'],
			'media_to_delete' => ['array', 'max:'.$sizeOfExistingMedia],
			'media_to_delete.*' => ['string', 'required'],
			'creator' => ['nullable', 'exists:users,username'],
			'genre' => ['nullable', 'exists:genres,slug'],
		];
	}
}
