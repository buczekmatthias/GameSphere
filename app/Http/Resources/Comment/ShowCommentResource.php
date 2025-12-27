<?php

namespace App\Http\Resources\Comment;

use App\Http\Resources\User\SimpleProfileResource;
use App\Services\UserPermissions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShowCommentResource extends JsonResource
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
			'media' => $this->media ?
					collect($this->media)
					->map(
						function (string $file) {
							$path = "discussions/{$this->discussion->slug}/{$this->slug}/{$file}";

							return [
								'filename' => $file,
								'path' => asset(Storage::url($path)),
								'type' => str_starts_with(Storage::mimeType($path), "image/") ? 'image' : 'video'
							];
						}
					)->toArray()
					: [],
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
					'created_at' => $this->discussion->created_at->format('Y-m-d')
				]
			),
			'permissions' => [
				'update' => UserPermissions::checkPermissions('update', $this->resource),
				'destroy' => UserPermissions::checkPermissions('delete', $this->resource),
			],
			'created_at' => $this->created_at->format("Y-m-d H:i")
		];
	}
}
