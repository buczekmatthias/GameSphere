<?php

namespace App\Http\Resources\Games;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ShowGameResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		$ratings = $this->reviews()->select('ratings')->pluck('ratings');
		$score = round($ratings->flatMap(fn ($rating) => array_values($rating))->avg(), 1);

		return [
			...EditGameResource::make($this)->toArray($request),
			'shortTitle' => Str::limit($this->title, 25, preserveWords: true),
			'creator' => ['name' => $this->creator->name, 'username' => $this->creator->username],
			'score' => $score,
			'reviews_count' => $this->whenCounted('reviews')
		];
	}
}
