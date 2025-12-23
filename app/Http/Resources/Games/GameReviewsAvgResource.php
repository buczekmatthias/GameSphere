<?php

namespace App\Http\Resources\Games;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameReviewsAvgResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'score' => $this->whenLoaded(
				'reviews',
				round(
					$this->reviews->flatMap(
						fn ($review) => array_values($review->ratings)
					)->avg(),
					1
				)
			)
		];
	}
}
