<?php

namespace App\Http\Resources\Discussion;

use App\Services\MorphTypeToLowerString;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListDiscussionResource extends JsonResource
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
			'author' => $this->whenLoaded(
				'author',
				fn () => new AuthorResource($this->author)
			),
			'discussable' => $this->whenLoaded(
				'discussable',
				fn () => str_contains($this->discussable_type, 'Game')
					? new GameResource($this->discussable)
					: new GenreResource($this->discussable)
			),
			'discussable_type' => $this->whenLoaded(
				'discussable',
				fn () => MorphTypeToLowerString::getTransformedString($this->discussable_type)
			),
			'comments_count' => $this->whenCounted('comments'),
			'created_at' => $this->created_at->format('Y-m-d')
		];
	}
}
