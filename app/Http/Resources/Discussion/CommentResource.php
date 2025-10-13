<?php

namespace App\Http\Resources\Discussion;

use App\Services\UserPermissions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommentResource extends JsonResource
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
						fn (string $file) => [
							'filename' => $file,
							'path' => asset(Storage::url("discussions/{$this->discussion->slug}/{$this->slug}/{$file}")),
							'type' => str_contains('.mp4', $file) ? 'video' : 'image'
						]
					)->toArray()
					: [],
			'user' => $this->whenLoaded(
				'user',
				fn () => new AuthorResource($this->user)
			),
			'discussion' => $this->whenLoaded(
				'discussion',
				fn () => [
					'title' => $this->discussion->title,
					'slug' => $this->discussion->slug,
					'author' => new AuthorResource($this->discussion->author),
					'comments_count' => $this->discussion->comments()->count(),
					'created_at' => $this->discussion->created_at->format('Y-m-d')
				]
			),
			'permissions' => [
				'update' => UserPermissions::checkPermissions('update', $this->resource),
				'destroy' => UserPermissions::checkPermissions('delete', $this->resource),
			],
			'created_at' => $this->created_at->format('Y-m-d')
		];
	}
}
