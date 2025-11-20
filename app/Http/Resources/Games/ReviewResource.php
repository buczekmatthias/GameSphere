<?php

namespace App\Http\Resources\Games;

use App\Http\Resources\User\SimpleProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

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
			'shortSlug' => Str::limit($this->slug, 20),
			'content' => $this->content,
			'ratings' => $this->ratings,
			'avg_rating' => round($this->average_rating, 1),
			'is_verified' => $this->is_verified,
			'user' => $this->whenLoaded(
				'user',
				fn () => [
					...SimpleProfileResource::make($this->user)->toArray($request),
					'is_email_verified' => $this->user->email_verified_at !== null
				]
			),
			'game' => $this->whenLoaded(
				'game',
				fn () => ['title' => $this->game->title, 'slug' => $this->game->slug]
			),
			'created_at' => $this->created_at->format('Y-m-d')
		];
	}
}
