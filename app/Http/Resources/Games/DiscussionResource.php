<?php

namespace App\Http\Resources\Games;

use App\Http\Resources\User\SimpleProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class DiscussionResource extends JsonResource
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
			'title' => Str::limit($this->title, 55, preserveWords: true),
			'author' => $this->whenLoaded(
				'author',
				fn () => SimpleProfileResource::make($this->author)->toArray($request),
			),
			'comments_count' => $this->whenCounted('comments'),
			'created_at' => $this->created_at->format('Y-m-d')
		];
	}
}
