<?php

namespace App\Http\Resources\Games;

use App\Http\Resources\PaginatedContentResource;
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
		$tab = $request->route('tab', 'reviews');

		$data = [
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

		if ($tab === 'reviews') {
			$data['reviews'] = PaginatedContentResource::make(
				$this->reviews()
					->with(['user'])
					->orderBy('created_at', 'DESC')
					->paginate(30)
			)
				->additional(['data_resource' => GameReviewResource::class])
				->toArray($request);
		}

		if ($tab === 'discussions' || !$this->isGameReleased()) {
			$data['discussions'] = PaginatedContentResource::make(
				$this->discussions()
					->with('author')
					->withCount('comments')
					->orderBy('created_at', 'DESC')
					->orderBy('id', 'DESC')
					->paginate(30)
			)
				->additional(['data_resource' => GameDiscussionResource::class])
				->toArray($request);
		}

		return $data;
	}
}
