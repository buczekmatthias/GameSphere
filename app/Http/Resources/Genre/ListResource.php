<?php

namespace App\Http\Resources\Genre;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		$userFavoriteGenres = $request->user() ? $request->user()->genres->pluck('slug')->toArray() : [];

		return [
			'slug' => $this->slug,
			'name' => $this->name,
			'discussions_count' => $this->whenCounted('discussions'),
			'games_count' => $this->whenCounted('games'),
			'is_user_favorite' => in_array($this->slug, $userFavoriteGenres)
		];
	}
}
