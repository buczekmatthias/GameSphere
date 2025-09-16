<?php

namespace App\Http\Resources\Admin\Discussion;

use App\Http\Resources\Discussion\AuthorResource;
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
		if ($this->whenLoaded('reportable')) {
			$s = MorphTypeToLowerString::getTransformedString($this->discussable_type);
		}

		return [
			'slug' => $this->slug,
			'title' => $this->title,
			'author' => $this->whenLoaded(
				'author',
				fn () => new AuthorResource($this->author)
			),
			'discussable' => $this->whenLoaded(
				'discussable',
				fn () => $s === 'game'
					? route('games.show', ['game' => $this->discussable->slug])
					: route('genres.show', ['genre' => $this->discussable->slug])
			),
			'discussable_type' => $this->whenLoaded(
				'discussable',
				fn () => $s
			),
			'comments_count' => $this->whenCounted('comments'),
			'reports_count' => $this->whenCounted('reports'),
			'created_at' => $this->created_at->format('Y-m-d')
		];
	}
}
