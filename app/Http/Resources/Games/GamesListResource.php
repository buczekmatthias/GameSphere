<?php

namespace App\Http\Resources\Games;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GamesListResource extends GameReviewResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'slug' => $this->slug,
			'title' => $this->title,
			'thumbnail' => asset(Storage::url("games/{$this->slug}/{$this->thumbnail}")),
			...parent::toArray($request)
		];
	}
}
