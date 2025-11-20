<?php

namespace App\Http\Resources\Admin\Game;

use App\Http\Resources\User\SimpleProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class GameListResource extends JsonResource
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
			'description' => $this->description,
			'short_description' => Str::limit($this->description, 35, preserveWords: true),
			'media_count' => $this->media ? count($this->media) : 0,
			'released_at' => $this->released_at->format('Y-m-d'),
			'genre' => $this->whenLoaded(
				'genre',
				fn () => ['name' => $this->genre->name, 'slug' => $this->genre->slug]
			),
			'creator' => $this->whenLoaded(
				'creator',
				fn () => SimpleProfileResource::make($this->creator)->toArray($request)
			),
			'created_at' => $this->created_at->format('Y-m-d'),
		];
	}
}
