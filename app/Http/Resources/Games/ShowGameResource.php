<?php

namespace App\Http\Resources\Games;

use App\Services\UserPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShowGameResource extends EditGameResource
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
			'shortTitle' => Str::limit($this->title, 25, preserveWords: true),
			'reviews_count' => $this->whenCounted('reviews'),
			'permissions' => [
				'update' => UserPermissions::checkPermissions('update', $this->resource),
				'destroy' => UserPermissions::checkPermissions('delete', $this->resource),
			],
			'is_released' => $this->isGameReleased(),
			'score' => $this->score ? round($this->score, 1) : null
		];
	}
}
