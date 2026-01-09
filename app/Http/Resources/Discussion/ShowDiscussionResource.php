<?php

namespace App\Http\Resources\Discussion;

use App\Http\Resources\PaginatedContentResource;
use App\Http\Resources\User\SimpleProfileResource;
use App\Services\MorphTypeToLowerString;
use App\Services\UserPermissions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ShowDiscussionResource extends JsonResource
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
			'shortTitle' => Str::limit($this->title, 25, preserveWords: true),
			'title' => $this->title,
			'author' => $this->whenLoaded(
				'author',
				fn () => SimpleProfileResource::make($this->author)->toArray($request)
			),
			'discussable' => $this->whenLoaded(
				'discussable',
				fn () => str_contains($this->discussable_type, 'Game')
					? [
						'slug' => $this->discussable->slug,
						'title' => $this->discussable->title
					]
					: [
						'slug' => $this->discussable->slug,
						'name' => $this->discussable->name
					]
			),
			'discussable_type' => $this->whenLoaded(
				'discussable',
				fn () => MorphTypeToLowerString::getTransformedString($this->discussable_type)
			),
			'comments' => PaginatedContentResource::make(
				$this->comments()
					->select(['content', 'media', 'created_at', 'slug', 'user_id'])
					->with('user:id,name,username,avatar')
					->orderBy('created_at', 'ASC')
					->orderBy('id', 'ASC')
					->paginate(10)
			)
				->additional(['data_resource' => DiscussionCommentResource::class, 'data_to_pass' => ['discussion' => ['slug' => $this->slug]]])
				->toArray($request),
			'comments_count' => $this->whenCounted('comments'),
			'created_at' => $this->created_at->format('Y-m-d'),
			'permissions' => [
				'update' => UserPermissions::checkPermissions('update', $this->resource),
				'destroy' => UserPermissions::checkPermissions('delete', $this->resource),
				'lock' => UserPermissions::checkPermissions('lock', $this->resource)
			],
			'is_locked' => $this->is_locked
		];
	}
}
