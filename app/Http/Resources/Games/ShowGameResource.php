<?php

namespace App\Http\Resources\Games;

use App\Http\Resources\User\SimpleProfileResource;
use App\Services\UserPermissions;
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
			'creator' => SimpleProfileResource::make($this->creator)->toArray($request),
			'score' => $score,
			'reviews_count' => $this->whenCounted('reviews'),
			'permissions' => [
				'update' => UserPermissions::checkPermissions('update', $this->resource),
				'destroy' => UserPermissions::checkPermissions('delete', $this->resource),
			],
			'is_released' => $this->isGameReleased()
		];
	}
}
