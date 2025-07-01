<?php

namespace App\Http\Resources\Discussion;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
			'created_at' => $this->created_at->format('Y-m-d')
		];
	}
}
