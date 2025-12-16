<?php

namespace App\Http\Resources\Discussion;

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
		$comments = $this->comments()->with('user')->orderBy('created_at', 'ASC')->orderBy('id', 'ASC')->paginate(10);
		$from = (($comments->currentPage() - 1) * $comments->perPage()) + 1;
		$to = ($comments->currentPage() - 1) * $comments->perPage() + $comments->count();

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
					? new DiscussionGameResource($this->discussable)
					: new DiscussionGenreResource($this->discussable)
			),
			'discussable_type' => $this->whenLoaded(
				'discussable',
				fn () => MorphTypeToLowerString::getTransformedString($this->discussable_type)
			),
			'comments' => [
				'data' => DiscussionCommentResource::collection($comments),
				'meta' => [
					'current_page' => $comments->currentPage(),
					'from' => $from,
					'last_page' => $comments->lastPage(),
					'per_page' => $comments->perPage(),
					'to' => $to,
					'total' => $comments->total(),
				]
			],
			'comments_count' => $this->whenCounted('comments'),
			'created_at' => $this->created_at->format('Y-m-d'),
			'permissions' => [
				'update' => UserPermissions::checkPermissions('update', $this->resource),
				'destroy' => UserPermissions::checkPermissions('delete', $this->resource),
			],
		];
	}
}
