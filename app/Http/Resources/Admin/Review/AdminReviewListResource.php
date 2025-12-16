<?php

namespace App\Http\Resources\Admin\Review;

use App\Http\Resources\User\SimpleProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class AdminReviewListResource extends JsonResource
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
			'content' => Str::limit($this->content, 20, preserveWords: true),
			'user' => $this->whenLoaded(
				'user',
				fn () => SimpleProfileResource::make($this->user)->toArray($request)
			),
			'game' => $this->whenLoaded(
				'game',
				fn () => ['title' => $this->game->title, 'slug' => $this->game->slug]
			),
			'reports_count' => $this->whenCounted('reports'),
			'created_at' => $this->created_at->format('Y-m-d')
		];
	}
}
