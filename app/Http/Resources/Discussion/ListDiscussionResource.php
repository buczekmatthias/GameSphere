<?php

namespace App\Http\Resources\Discussion;

use App\Http\Resources\User\SimpleProfileResource;
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
				fn () => SimpleProfileResource::make($this->author)->toArray($request)
			),
			'discussable' => $this->whenLoaded(
				'discussable',
				fn () => $s === 'game'
				? ['slug' => $this->discussable->slug, 'title' => $this->discussable->title]
				: ['slug' => $this->discussable->slug, 'name' => $this->discussable->name]
			),
			'discussable_type' => $this->whenLoaded(
				'discussable',
				fn () => $s
			),
			'comments_count' => $this->whenCounted('comments'),
			'created_at' => $this->created_at->format('Y-m-d')
		];
	}
}
