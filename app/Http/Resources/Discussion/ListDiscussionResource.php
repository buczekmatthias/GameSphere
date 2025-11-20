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
		return [
			'slug' => $this->slug,
			'title' => $this->title,
			'author' => $this->whenLoaded(
				'author',
				fn () => SimpleProfileResource::make($this->author)->toArray($request)
			),
			'discussable' => $this->whenLoaded(
				'discussable',
				fn () => ['slug' => $this->discussable->slug, 'title' => $this->discussable->title]
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
