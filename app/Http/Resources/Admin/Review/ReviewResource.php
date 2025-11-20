<?php

namespace App\Http\Resources\Admin\Review;

use App\Http\Resources\User\SimpleProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
			'content' => $this->content,
			'ratings' => $this->ratings,
			'is_verified' => $this->is_verified,
			'user' => $this->whenLoaded(
				'user',
				fn () => SimpleProfileResource::make($this->user)->toArray($request)
			),
			'game' => $this->whenLoaded(
				'game',
				fn () => ['title' => $this->game->title, 'slug' => $this->game->slug]
			),
			'created_at' => $this->created_at->format('Y-m-d')
		];
	}
}
