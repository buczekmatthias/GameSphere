<?php

namespace App\Http\Resources\Genre;

use App\Services\ShorterNumbers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenreListResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			...BaseGenre::make($this)->toArray($request),
			'discussions_count' => $this->whenCounted('discussions', ShorterNumbers::convertIntToHumanReadable($this->discussions_count)),
			'games_count' => $this->whenCounted('games', ShorterNumbers::convertIntToHumanReadable($this->games_count)),
		];
	}
}
