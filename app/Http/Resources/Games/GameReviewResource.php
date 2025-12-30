<?php

namespace App\Http\Resources\Games;

use App\Http\Resources\PaginatedContentResource;
use App\Http\Resources\Report\UserReportsTableResource;
use App\Http\Resources\User\SimpleProfileResource;
use App\Services\UserPermissions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class GameReviewResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		$data = [
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
				fn () => [
					...GamesListResource::make($this->game)->toArray($request),
					'description' => $this->game->description
				]
			),
			'created_at' => $this->created_at->format('Y-m-d'),
			'permissions' => [
				'destroy' => UserPermissions::checkPermissions('delete', $this->resource),
			],
		];

		if ($request->user()?->isStaff()) {
			$data['reports'] = PaginatedContentResource::make($this->reports()->with(['user'])->orderBy('created_at', 'DESC')->paginate(25))->additional(['data_resource' => UserReportsTableResource::class])->toArray($request);
		}

		return $data;
	}
}
