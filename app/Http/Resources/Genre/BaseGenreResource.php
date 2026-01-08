<?php

namespace App\Http\Resources\Genre;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseGenreResource extends JsonResource
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
			'name' => $this->name,
			'is_user_favorite' => $request->user()?->genres->pluck('slug')->contains($this->slug)
		];
	}
}
