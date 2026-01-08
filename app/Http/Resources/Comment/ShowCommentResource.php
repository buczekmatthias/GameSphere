<?php

namespace App\Http\Resources\Comment;

use App\Http\Resources\User\SimpleProfileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShowCommentResource extends UserProfileCommentResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			...parent::toArray($request),
			'shortSlug' => Str::limit($this->slug, 20),
			'user' => $this->whenLoaded(
				'user',
				fn () => SimpleProfileResource::make($this->user)->toArray($request),
			),
			'discussion' => $this->whenLoaded(
				'discussion',
				fn () => [
					'title' => $this->discussion->title,
					'slug' => $this->discussion->slug,
					'author' => $this->when($this->discussion->relationLoaded('author'), fn () => SimpleProfileResource::make($this->discussion->author)->toArray($request)),
					'comments_count' => $this->discussion->comments()->count(),
					'created_at' => $this->discussion->created_at?->format('Y-m-d')
				]
			),
		];
	}
}
