<?php

namespace App\Http\Resources\Discussion;

use App\Http\Resources\User\SimpleProfileResource;
use App\Services\UserPermissions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DiscussionCommentResource extends JsonResource
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
			'permissions' => [
				'update' => UserPermissions::checkPermissions('update', $this->resource),
				'destroy' => UserPermissions::checkPermissions('delete', $this->resource),
			],
			'created_at' => $this->created_at->format("Y-m-d H:i")
		];
	}
}
