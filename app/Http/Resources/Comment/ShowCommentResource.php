<?php

namespace App\Http\Resources\Comment;

use App\Http\Resources\PaginatedContentResource;
use App\Http\Resources\Report\UserReportsTableResource;
use App\Http\Resources\User\SimpleProfileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShowCommentResource extends UserProfileCommentResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		$data = [
			...parent::toArray($request),
			'shortSlug' => Str::limit($this->slug, 20),
			'user' => $this->whenLoaded(
				'user',
				fn () => SimpleProfileResource::make($this->user)->toArray($request),
			),
			'discussion' => $this->whenLoaded(
				'discussion',
				fn () => [
					'title' => $this->discussion->title,
					'slug' => $this->discussion->slug,
					'author' => $this->when($this->discussion->relationLoaded('author'), fn () => SimpleProfileResource::make($this->discussion->author)->toArray($request)),
					'comments_count' => $this->discussion->comments()->count(),
					'created_at' => $this->discussion->created_at?->format('Y-m-d')
				]
			),
		];

		if ($request->user()?->isStaff() && $request->routeIs('comments.show')) {
			$data['reports'] = PaginatedContentResource::make(
				$this->reports()
					->select(['slug', 'status', 'reason', 'created_at', 'user_id', 'reportable_id', 'reportable_type'])
					->with(['user:id,name,username'])
					->orderBy('created_at', 'DESC')
					->paginate(25)
			)
				->additional(['data_resource' => UserReportsTableResource::class])
				->toArray($request);
		}

		return $data;
	}
}
